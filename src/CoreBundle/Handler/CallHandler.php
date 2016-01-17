<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:29
 */

namespace CoreBundle\Handler;

use ApiBundle\Model\Request\Call\CallPostSendRequest;
use ApiBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\Timecontrol;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\GameCallHandlerException;
use CoreBundle\Exception\Processor\CallProcessorException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\GameCall\GameCallType;
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
    public function getCalls(User $user, $callType)
    {
        switch ($callType) {
            case GameCallType::FROM:
                $fieldForUser = 'fromUser';
                break;
            case GameCallType::TO:
                $fieldForUser = 'toUser';
                break;
            default:
                throw new GameCallHandlerException("Unknown call type for calls", ResponseStatusCode::FORBIDDEN);
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
     * @return mixed
     */
    public function processPostSend(CallPostSendRequest $sendRequest)
    {
        $me = $this->container->get("core.handler.user")->getUserByLoginAndToken($sendRequest->getLogin(),
            $sendRequest->getToken());

        if (!$me instanceof User) {
            throw new CallProcessorException("User is not found", ResponseStatusCode::FORBIDDEN,
                ["login" => "Forbidden for user with this credentials"]);
        }

        /** @var User $opponent */
        $opponent = $this->container->get("core.handler.user")->getRepository()->findOneByLogin($sendRequest->getPlayer());

        if (!$opponent instanceof User) {
            throw new CallProcessorException("User is not found", ResponseStatusCode::NOT_FOUND,
                ["player" => "Opponent with this login is not found"]);
        }

        $timecontrol = $this->container->get("core.handler.timecontrol")->getRepository()->find($sendRequest->getTimecontrol());

        if (!$timecontrol instanceof Timecontrol) {
            throw new CallProcessorException("Timecontrol is not found", ResponseStatusCode::NOT_FOUND,
                ["timecontrol" => "Timecontrol is not found"]);
        }

        if ($sendRequest->getColor() == GameColor::RANDOM) {
            $sendRequest->setColor(
                [GameColor::WHITE, GameColor::BLACK][mt_rand(0, 1)]
            );
        }

        $newCalls = [];

        for ($i = 0; $i < $sendRequest->getGamesCount(); $i++) {
            $newCalls[] = $this->createGameCall($me, $opponent,
                $game = $this->container->get("core.handler.game")->createMyGame($me, $opponent, $timecontrol, $sendRequest->getColor()));
            $this->manager->persist($game);
        }

        $this->manager->flush();

        return $sendRequest->getGamesCount() == 1 ? $newCalls[0] : $newCalls;
    }
}