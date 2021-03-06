<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:29
 */

namespace CoreBundle\Handler;

use CoreBundle\Exception\Handler\GameCallHandlerException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\Event\Call\CallEvent;
use CoreBundle\Model\Event\Call\CallEvents;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\CallDeleteDeclineRequest;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallGetRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Call\CallType;
use CoreBundle\Processor\CallProcessorInterface;
use CoreBundle\Repository\GameCallRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class CallHandler
 * @package CoreBundle\Handler
 */
class CallHandler implements CallProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

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
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:GameCall');
    }

    /**
     * @param CallGetRequest $getRequest
     * @return array|GameCall[]
     */
    public function processGet(CallGetRequest $getRequest) : array
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($getRequest, $this->getRequestError());

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
     * @param GameParams $gameParams
     * @param User $opponent
     * @return GameCall
     */
    private function createGameCallToUser(User $me, GameParams $gameParams, User $opponent) : GameCall
    {
        $call = $this->createEntity();

        $call->setFromUser($me)
             ->setToUser($opponent)
             ->setGameParams($gameParams);

        $this->manager->persist($call);

        return $call;
    }

    /**
     * @param User $me
     * @param GameParams $gameParams
     * @return GameCall
     */
    private function createCommonGameCall(User $me, GameParams $gameParams) : GameCall
    {
        $call = $this->createEntity();

        $call->setFromUser($me)
             ->setGameParams($gameParams);

        if ($this->isTheSameCommonCallExists($call, $me)) {
            $this->getRequestError()
                 ->addError("player", "You already created call with the same params")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->persist($call);

        $this->container->get("event_dispatcher")->dispatch(
            CallEvents::NEW_CALL,
            (new CallEvent())->setCall($call)
        );

        return $call;
    }

    /**
     * @param CallPostSendRequest $request
     * @return GameCall
     */
    public function processPostSend(CallPostSendRequest $request) : GameCall
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        if ($me->isBanned()) {
            $this->getRequestError()->addError("user", "You are banned")
                                    ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if (!$request->getColor() || $request->getColor() == GameColor::RANDOM) {
            $request->setColor(
                !$me->getLastColor() ? [GameColor::WHITE, GameColor::BLACK][mt_rand(0, 1)] :
                    GameColor::getOppositeColor($me->getLastColor())
            );
        }

        $gameParams = new GameParams();
        $gameParams->setColor( GameColor::getOppositeColor($request->getColor()) )
                   ->setTimeBase($request->getTime()->getBase())
                   ->setTimeIncrement($request->getTime()->getIncrement())
                   ->setRate($request->isRate());

        if (!$request->getPlayer()) {
            $gameCall = $this->createCommonGameCall($me, $gameParams);

            $this->manager->flush();

            return $gameCall;
        }

        try {
            $opponent = $this->container->get("core.handler.user")
                                        ->getRepository()
                                        ->findOneByLogin($request->getPlayer());
        } catch (UserNotFoundException $e) {
            $this->getRequestError()
                 ->addError("player", "Opponent with this login is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var User $opponent */
        $gameCall = $this->createGameCallToUser($me, $gameParams, $opponent);

        $this->manager->flush();

        $this->container->get('logger')->debug(__METHOD__.'. After flush rating '.$gameCall->getFromUser().' = '.$gameCall->getFromUser()->getRating().' and rating '.$gameCall->getToUser().' = '.$gameCall->getToUser()->getRating());

        return $gameCall;
    }

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest) : GameCall
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($removeRequest, $this->getRequestError());

        $call = $this->repository->find($removeRequest->getCallId());

        if (!$call instanceof GameCall) {
            $this->getRequestError()->addError("call_id", "Call is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getFromUser() != $me) {
            $this->getRequestError()->addError("login", "This is not your call");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->remove($call);

        $this->manager->flush();

        return $call;
    }

    /**
     * @param CallDeleteAcceptRequest $acceptRequest
     * @return Game
     */
    public function processDeleteAccept(CallDeleteAcceptRequest $acceptRequest) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($acceptRequest, $this->getRequestError());

        if ($me->isBanned()) {
            $this->getRequestError()->addError("user", "You are banned")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $call = $this->repository->find($acceptRequest->getCallId());

        if (!$call instanceof GameCall) {
            $this->getRequestError()->addError("call_id", "Call is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getFromUser() == $me) {
            $this->getRequestError()->addError("call_id", "This challenge is mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $game = $this->container->get("core.handler.game")->createMyGame(
            $call->getFromUser(),
            $me,
            GameColor::getOppositeColor($call->getGameParams()->getColor()),
            $call->getGameParams()->isRate()
        );
        
        $this->container->get("core.handler.game")->changeGameStatus($game, GameStatus::PLAY);
        
        $game->setTimeWhite($call->getGameParams()->getTimeBase())
             ->setTimeBlack($call->getGameParams()->getTimeBase());

        $game->setGameParams($call->getGameParams());

        $this->manager->persist($game);

        foreach ($this->getUserCalls($call->getFromUser()) as $call) {
            $this->manager->remove($call);
        }

        foreach ($this->getUserCalls($me) as $call) {
            $this->manager->remove($call);
        }

        $this->manager->flush();

        return $this->container->get("core.handler.game")->getUserGame($game, $me);
    }

    /**
     * @param CallDeleteDeclineRequest $declineRequest
     * @return GameCall
     */
    public function processDeleteDecline(CallDeleteDeclineRequest $declineRequest) : GameCall 
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($declineRequest, $this->getRequestError());

        $call = $this->repository->find($declineRequest->getCallId());

        if (!$call instanceof GameCall) {
            $this->getRequestError()->addError("call_id", "Call is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if ($call->getToUser() != $me) {
            $this->getRequestError()->addError("login", "This is not call to you");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->remove($call);

        $this->manager->flush();

        return $call;
    }

    /**
     * @param string $login
     * @param string $fieldForUser
     * @param array $callIds
     * @return GameCall[]
     */
    public function getUserCallsByLogin(string $login, string $fieldForUser = 'fromUser', array $callIds = [])
    {
        if (!$login) {
            return $this->getQueryBuilderForLastCalls()
                        ->andWhere('game_call.toUser IS NULL')
                        ->getQuery()->useQueryCache(false)->execute();
        }

        $user = $this->container->get("core.handler.user")->getRepository()
                                ->findOneBy(["login" => $login]);

        if (!$user instanceof User) {
            throw new GameCallHandlerException("User $login is not found");
        }

        return $this->getUserCalls($user, $fieldForUser);
    }

    /**
     * @param User $user
     * @param string $fieldForUser
     * @return array|\CoreBundle\Entity\GameCall[]
     */
    private function getUserCalls(User $user, string $fieldForUser = 'fromUser') : array
    {
        $queryBuilder = $this->getQueryBuilderForLastCalls();

        if ($fieldForUser == 'toUser') {
            $queryBuilder->andWhere('game_call.toUser IS NULL OR game_call.toUser = :user');
        } elseif ($fieldForUser == 'fromUser') {
            $queryBuilder->andWhere('game_call.fromUser = :user');
        }

        $queryBuilder->setParameter('user', $user);

        return $queryBuilder->getQuery()->useQueryCache(false)->execute();
    }

    /**
     * @param GameCall $call
     * @param User $user
     * @return bool
     */
    private function isTheSameCommonCallExists(GameCall $call, User $user) : bool
    {
        return !empty(
            array_filter(
                $this->getUserCalls($user),
                function(GameCall $existingCall) use ($call) {
                    return $this->isTwoCommonCallsEqual($existingCall, $call);
                }
            )
        );
    }

    /**
     * @param GameCall $existingCall
     * @param GameCall $call
     * @return bool
     */
    private function isTwoCommonCallsEqual(GameCall $existingCall, GameCall $call) : bool
    {
        return !$existingCall->getToUser() && !$call->getToUser() && $existingCall->getGameParams() == $call->getGameParams();
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilderForLastCalls() : QueryBuilder
    {
        $queryBuilder = $this->repository->createQueryBuilder('game_call');

        $queryBuilder->where('game_call.createdAt > :limitAgo')
            ->setParameter(
                'limitAgo',
                $this->container->get("core.service.date")->getDateTime('-' . $this->container->getParameter('app_call.lifetime') . 'second')
            );

        return $queryBuilder;
    }

    /**
     * @return GameCall
     */
    public function createEntity()
    {
        return (new GameCall())->setCreatedAt($this->container->get("core.service.date")->getDateTime());
    }
}