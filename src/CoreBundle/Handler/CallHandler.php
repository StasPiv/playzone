<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:29
 */

namespace CoreBundle\Handler;

use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallPutAcceptRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\Timecontrol;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\GameCallHandlerException;
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
     * @param User $user
     * @param $callType
     * @return GameCall[]
     */
    public function getCalls(User $user, $callType = CallType::FROM)
    {
        switch ($callType) {
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

        $calls = $this->repository->findBy([$fieldForUser => $user]);

        foreach ($calls as $call) {
            /** @var GameCall $call */
            $this->container->get("core.handler.game")->defineUserColorForGame($user, $call->getGame());
        }

        return $calls;
    }

    /**
     * @param User $me
     * @param User $opponent
     * @param Game $game
     * @return GameCall
     */
    public function createGameCall(User $me, User $opponent, Game $game)
    {
        $call = new GameCall();

        $call->setFromUser($me)
            ->setToUser($opponent)
            ->setGame($game);

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
        $me = $this->container->get("core.handler.security")->getMeIfCredentialsIsOk($sendRequest, $sendError);

        /** @var User $opponent */
        $opponent = $this->container->get("core.handler.user")->getRepository()->findOneByLogin($sendRequest->getPlayer());

        if (!$opponent instanceof User) {
            $sendError->setPlayer("Opponent with this login is not found");
            $sendError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        $timecontrol = $this->container->get("core.handler.timecontrol")->getRepository()->find($sendRequest->getTimecontrol());

        if (!$timecontrol instanceof Timecontrol) {
            $sendError->setTimecontrol("Timecontrol is not found");
            $sendError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$sendRequest->getColor() || $sendRequest->getColor() == GameColor::RANDOM) {
            $sendRequest->setColor(
                [GameColor::WHITE, GameColor::BLACK][mt_rand(0, 1)]
            );
        }

        $newCalls = [];

        for ($i = 0; $i < $sendRequest->getGamesCount(); $i++) {
            $game = $this->container->get("core.handler.game")->createMyGame($me, $opponent, $timecontrol,
                $sendRequest->getColor());
            $newCalls[] = $this->createGameCall($me, $opponent, $game);
            $this->manager->persist($game);
        }

        $this->manager->flush();

        return $newCalls;
    }

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @param CallDeleteRemoveRequest $removeError
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest, CallDeleteRemoveRequest $removeError)
    {
        $me = $this->container->get("core.handler.security")->getMeIfCredentialsIsOk($removeRequest, $removeError);

        $call = $this->repository->find($removeRequest->getCallId());

        if (!$call instanceof GameCall) {
            $removeError->setCallId("Call is not found");
            $removeError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getFromUser() != $me) {
            $removeError->setLogin("This is not your call");
            $removeError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($call->getGame()->getStatus() != GameStatus::CALL) {
            $removeError->setCallId("Game is not call already");
            $removeError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->container->get("core.handler.game")->defineUserColorForGame($me, $call->getGame());

        $this->manager->remove($call);

        $this->manager->flush();

        return $call;
    }

    /**
     * @param CallPutAcceptRequest $acceptRequest
     * @param CallPutAcceptRequest $acceptError
     * @return Game
     */
    public function processPutAccept(CallPutAcceptRequest $acceptRequest, CallPutAcceptRequest $acceptError)
    {
        $me = $this->container->get("core.handler.security")->getMeIfCredentialsIsOk($acceptRequest, $acceptError);

        $call = $this->repository->find($acceptRequest->getCallId());

        if (!$call instanceof GameCall) {
            $acceptError->setCallId("Call is not found");
            $acceptError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getToUser() != $me) {
            $acceptError->setLogin("This is not call to you");
            $acceptError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($call->getGame()->getStatus() != GameStatus::CALL) {
            $acceptError->setCallId("The call is already accepted");
            $acceptError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->remove($call);
        $call->getGame()->setStatus(GameStatus::PLAY);

        $this->manager->persist($call->getGame());
        $this->manager->flush();

        return $call->getGame();
    }
}