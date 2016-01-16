<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:59
 */

namespace CoreBundle\Handler;

use ApiBundle\Model\Request\Game\GameGetListRequest;
use ApiBundle\Model\Request\Game\GamePostCallRequest;
use ApiBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\Timecontrol;
use CoreBundle\Exception\Processor\GameProcessorException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\User\UserType;
use CoreBundle\Processor\GameProcessorInterface;
use CoreBundle\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use CoreBundle\Entity\User;

class GameHandler implements GameProcessorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var GameRepository
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
        $this->repository = $this->manager->getRepository('CoreBundle:Game');
    }

    /**
     * @param GameGetListRequest $listRequest
     * @return Game[]
     */
    public function processGetList(GameGetListRequest $listRequest)
    {
        if ($listRequest->getUser() == UserType::ME) {
            if (!$listRequest->getLogin() || !$listRequest->getToken()) {
                throw new GameProcessorException("Need to pass login and token for getting game list for current user",
                    ResponseStatusCode::FORBIDDEN);
            }

            $user = $this->container->get("core.handler.user")
                ->getUserByLoginAndToken($listRequest->getLogin(), $listRequest->getToken());

            return $this->getGamesForUser($listRequest, $user);
        }

        return [];
    }

    /**
     * @param GamePostCallRequest $callRequest
     * @return GameCall
     */
    public function processPostCall(GamePostCallRequest $callRequest)
    {
        $me = $this->container->get("core.handler.user")->getUserByLoginAndToken($callRequest->getLogin(),
            $callRequest->getToken());

        if (!$me instanceof User) {
            throw new GameProcessorException("User is not found", ResponseStatusCode::FORBIDDEN,
                ["login" => "Forbidden for user with this credentials"]);
        }

        /** @var User $opponent */
        $opponent = $this->container->get("core.handler.user")->getRepository()->findOneByLogin($callRequest->getPlayer());

        if (!$opponent instanceof User) {
            throw new GameProcessorException("User is not found", ResponseStatusCode::NOT_FOUND,
                ["player" => "Opponent with this login is not found"]);
        }

        $timecontrol = $this->container->get("core.handler.timecontrol")->getRepository()->find($callRequest->getTimecontrol());

        if (!$timecontrol instanceof Timecontrol) {
            throw new GameProcessorException("Timecontrol is not found", ResponseStatusCode::NOT_FOUND,
                ["timecontrol" => "Timecontrol is not found"]);
        }

        if ($callRequest->getColor() == GameColor::RANDOM) {
            $callRequest->setColor(
                [GameColor::WHITE, GameColor::BLACK][mt_rand(0, 1)]
            );
        }

        $newCalls = [];

        for ($i = 0; $i < $callRequest->getGamesCount(); $i++) {
            $newCalls[] = $this->container->get("core.handler.game.call")->createGameCall($me, $opponent,
                $game = $this->createMyGame($me, $opponent, $timecontrol, $callRequest->getColor()));
            $this->manager->persist($game);
        }

        $this->manager->flush();

        return $callRequest->getGamesCount() == 1 ? $newCalls[0] : $newCalls;
    }

    /**
     * @param GameGetListRequest $listRequest
     * @param User $user
     * @return Game[]
     */
    private function getGamesForUser(GameGetListRequest $listRequest, User $user)
    {
        if ($listRequest->getStatus() == GameStatus::CALL) {
            return $this->container->get("core.handler.game.call")->getCalls($user, $listRequest->getCallType());
        }

        $games = $this->manager
            ->createQuery(
                "SELECT g FROM CoreBundle:Game g
                          WHERE (g.userWhite = :user OR g.userBlack = :user) AND g.status = :status
                          ORDER BY g.id ASC"
            )
            ->setParameter("status", $listRequest->getStatus())
            ->setParameter("user", $user)
            ->getResult();

        foreach ($games as $game) {
            $this->defineUserColorForGame($user, $game);
            $this->defineUserMoveAndOpponentForGame($user, $game);
        }

        return $games;
    }

    /**
     * @param User $user
     * @param Game $game
     */
    public function defineUserColorForGame(User $user, Game $game)
    {
        switch (true) {
            case $game->getUserWhite() == $user:
                $game->setColor(GameColor::WHITE);
                break;
            case $game->getUserBlack() == $user:
                $game->setColor(GameColor::BLACK);
                break;
            default:
                throw new GameProcessorException("Unknown user {$user->getLogin()} for game {$game->getId()}");
        }
    }

    /**
     * @param User $user
     * @param Game $game
     */
    private function defineUserMoveAndOpponentForGame(User $user, Game $game)
    {
        $game->setUserMove($game->getUserToMove() == $user);

        switch (true) {
            case $game->getUserWhite() == $user:
                $game->setOpponent($game->getUserBlack());
                break;
            case $game->getUserBlack() == $user:
                $game->setOpponent($game->getUserWhite());
                break;
            default:
                throw new GameProcessorException("Unknown user {$user->getLogin()} for game {$game->getId()}");
        }
    }

    /**
     * @param User $me
     * @param User $opponent
     * @param Timecontrol $timecontrol
     * @param $myColor
     * @return Game
     */
    private function createMyGame(User $me, User $opponent, Timecontrol $timecontrol, $myColor)
    {
        $game = new Game();

        switch ($myColor) {
            case GameColor::WHITE:
                $game->setUserWhite($me)
                    ->setUserBlack($opponent);
                break;
            case GameColor::BLACK:
                $game->setUserBlack($me)
                    ->setUserWhite($opponent);
                break;
            default:
                throw new GameProcessorException("Color is incorrect", ResponseStatusCode::NOT_FOUND,
                    ["color" => "Color is incorrect"]);
        }

        $game->setStatus(GameStatus::CALL)
            ->setTimecontrol($timecontrol)
            ->setTimeLastMove(new \DateTime())
            ->setUserToMove($game->getUserWhite());

        $this->container->get("core.handler.game")->defineUserColorForGame($me, $game);

        return $game;
    }
}