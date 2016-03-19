<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:29
 */

namespace CoreBundle\Handler;

use CoreBundle\Exception\Handler\GameCallHandlerException;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\CallDeleteDeclineRequest;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallGetRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Call\CallType;
use CoreBundle\Processor\CallProcessorInterface;
use CoreBundle\Repository\GameCallRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CallHandler implements CallProcessorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var GameCallRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param Container $container
     * @param EntityManager $manager
     */
    public function __construct(Container $container, EntityManager $manager)
    {
        $this->setContainer($container);
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:GameCall');
    }

    /**
     * @param CallGetRequest $getRequest
     * @param CallGetRequest $getError
     * @return GameCall[]
     */
    public function processGet(CallGetRequest $getRequest, CallGetRequest $getError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($getRequest, $getError);

        switch ($getRequest->getType()) {
            case CallType::FROM:
                $fieldForUser = 'fromUser';
                break;
            case CallType::TO:
                $fieldForUser = 'toUser';
                break;
            default:
                $fieldForUser = 'fromUser';
                break;
        }

        return $this->getUserCalls($me, $fieldForUser);
    }

    /**
     * @param User $me
     * @param User $opponent can be null - in this case common call will be created
     * @param GameParams $gameParams
     * @return GameCall
     */
    public function createGameCall(User $me, $opponent, GameParams $gameParams)
    {
        $call = new GameCall();

        $call->setFromUser($me)
             ->setGameParams($gameParams);

        if ($opponent) {
            $call->setToUser($opponent);
        }

        $this->manager->persist($call);

        return $call;
    }

    /**
     * @param CallPostSendRequest $sendRequest
     * @param CallPostSendRequest $sendError
     * @return GameCall[]
     */
    public function processPostSend(CallPostSendRequest $sendRequest, CallPostSendRequest $sendError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($sendRequest, $sendError);

        if ($sendRequest->getPlayer()) {
            /** @var User $opponent */
            $opponent = $this->container->get("core.handler.user")->getRepository()->findOneByLogin($sendRequest->getPlayer());

            if (!$opponent instanceof User) {
                $sendError->setPlayer("Opponent with this login is not found");
                $sendError->throwException(ResponseStatusCode::NOT_FOUND);
            }
        } else {
            $opponent = null;
        }

        if (!$sendRequest->getColor() || $sendRequest->getColor() == GameColor::RANDOM) {
            $sendRequest->setColor(
                [GameColor::WHITE, GameColor::BLACK][mt_rand(0, 1)]
            );
        }

        $gameParams = new GameParams();
        $gameParams->setColor(
            $this->getOpponentColor(new GameColor($sendRequest->getColor()))
        );

        $gameCall = $this->createGameCall($me, $opponent, $gameParams);

        $this->manager->flush();

        return $gameCall;
    }

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @param CallDeleteRemoveRequest $removeError
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest, CallDeleteRemoveRequest $removeError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($removeRequest, $removeError);

        $call = $this->repository->find($removeRequest->getCallId());

        if (!$call instanceof GameCall) {
            $removeError->setCallId("Call is not found");
            $removeError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getFromUser() != $me) {
            $removeError->setLogin("This is not your call");
            $removeError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->remove($call);

        $this->manager->flush();

        return $call;
    }

    /**
     * @param CallDeleteAcceptRequest $acceptRequest
     * @param CallDeleteAcceptRequest $acceptError
     * @return Game
     */
    public function processDeleteAccept(CallDeleteAcceptRequest $acceptRequest, CallDeleteAcceptRequest $acceptError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($acceptRequest, $acceptError);

        $call = $this->repository->find($acceptRequest->getCallId());

        if (!$call instanceof GameCall) {
            $acceptError->setCallId("Call is not found");
            $acceptError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        $this->manager->remove($call);
        $game = $this->container->get("core.handler.game")->createMyGame(
            $call->getFromUser(),
            $me,
            $this->getOpponentColor($call->getGameParams()->getColor())->getValue()
        );
        $game->setStatus(GameStatus::PLAY);

        $this->manager->persist($game);
        $this->manager->flush();

        return $this->container->get("core.handler.game")->getUserGame($game, $me);
    }

    /**
     * @param GameColor $color
     * @return GameColor
     */
    private function getOpponentColor(GameColor $color)
    {
        switch ($color) {
            case GameColor::WHITE():
                return GameColor::BLACK();
            case GameColor::BLACK():
                return GameColor::WHITE();
            default:
                return GameColor::WHITE();
        }
    }

    /**
     * @param CallDeleteDeclineRequest $declineRequest
     * @param CallDeleteDeclineRequest $declineError
     * @return GameCall
     */
    public function processDeleteDecline(
        CallDeleteDeclineRequest $declineRequest,
        CallDeleteDeclineRequest $declineError
    ) {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($declineRequest, $declineError);

        $call = $this->repository->find($declineRequest->getCallId());

        if (!$call instanceof GameCall) {
            $declineError->setCallId("Call is not found");
            $declineError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getToUser() != $me) {
            $declineError->setLogin("This is not call to you");
            $declineError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->remove($call);

        $this->manager->flush();

        return $call;
    }

    /**
     * @param $login
     * @param string $fieldForUser
     * @param array $callIds
     * @return \CoreBundle\Entity\GameCall[]
     */
    public function getUserCallsByLogin($login, $fieldForUser = 'fromUser', array $callIds = [])
    {
        if (!$login) {
            return $this->repository->findBy(['toUser' => null]);
        }

        $user = $this->container->get("core.handler.user")->getRepository()
                                ->findOneBy(["login" => $login]);

        if (!$user instanceof User) {
            throw new GameCallHandlerException("User $login is not found");
        }

        return $this->getUserCalls($user, $fieldForUser, $callIds);
    }

    /**
     * @param $user
     * @param string $fieldForUser
     * @param array $callIds
     * @return \CoreBundle\Entity\GameCall[]
     */
    private function getUserCalls($user, $fieldForUser = 'fromUser', array $callIds = [])
    {
        $filter = [$fieldForUser => $user];

        if (!empty($callIds)) {
            $filter['id'] = $callIds;
        }

        if ($fieldForUser == 'toUser') {
            $filter['toUser'] = [null, $user];
        }

        return $this->repository->findBy($filter);
    }
}