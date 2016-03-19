<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:59
 */

namespace CoreBundle\Handler;

use CoreBundle\Exception\Handler\GameHandlerException;
use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
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
     * @param GameGetListRequest $listError
     * @return \CoreBundle\Entity\Game[]
     */
    public function processGetList(GameGetListRequest $listRequest, GameGetListRequest $listError)
    {
        switch ($listRequest->getUser()) {
            case UserType::ME:
                $user = $this->container->get("core.handler.user")
                    ->getUserByLoginAndToken($listRequest->getLogin(), $listRequest->getToken());

                if (!$user instanceof User) {
                    $listError->setLogin("Need to pass correct login and token for getting game list for current user");
                    $listError->throwException(ResponseStatusCode::FORBIDDEN);
                }

                return $this->getGamesForUser($user, $listRequest->getStatus());
            case UserType::ALL:
                return $this->repository->findBy(['status' => $listRequest->getStatus()]);
            default:
                return [];
        }
    }

    /**
     * @param GameGetRequest $gameRequest
     * @param GameGetRequest $gameError
     * @return mixed
     */
    public function processGet(GameGetRequest $gameRequest, GameGetRequest $gameError)
    {
        $game = $this->repository->find($gameRequest->getId());

        if (!$game instanceof Game) {
            $gameError->setId("Game is not found");
            $gameError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$gameRequest->getLogin()) {
            return $game;
        }

        $user = $this->container->get("core.handler.user")
                     ->getUserByLoginAndToken($gameRequest->getLogin(), $gameRequest->getToken());

        if (!in_array($user, [$game->getUserWhite(), $game->getUserBlack()])) {
            return $game;
        }

        return $this->getUserGame($user, $game);
    }

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @param GamePutPgnRequest $pgnError
     * @return \CoreBundle\Entity\Game
     */
    public function processPutPgn(GamePutPgnRequest $pgnRequest, GamePutPgnRequest $pgnError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($pgnRequest, $pgnError);

        $game = $this->repository->find($pgnRequest->getId());

        if (!$game instanceof Game) {
            $pgnError->setId("Game is not found");
            $pgnError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!in_array($me, [$game->getUserWhite(), $game->getUserBlack()])) {
            $pgnError->setId("Game is not mine");
            $pgnError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $pgn = $this->container->get("core.service.chess")->decodePgn($pgnRequest->getPgn());

        if (!$this->container->get("core.service.chess")->isValidPgn($pgn)) {
            $pgnError->setPgn("Pgn is incorrect");
            $pgnError->throwException(ResponseStatusCode::BAD_FORMAT);
        }

        $game->setPgn($pgn)
             ->setDraw("")
             ->setTimeWhite($pgnRequest->getTimeWhite())
             ->setTimeBlack($pgnRequest->getTimeBlack())
             ->setUserToMove($me == $game->getUserWhite() ? $game->getUserBlack() : $game->getUserWhite());

        if ($game->getTimeWhite() <= 0) {
            $game->setResultWhite(0)->setResultBlack(1)->setStatus(GameStatus::END);
        }

        if ($game->getTimeBlack() <= 0) {
            $game->setResultWhite(1)->setResultBlack(0)->setStatus(GameStatus::END);
        }

        $this->manager->flush($game);

        return $this->getUserGame($me, $game);
    }

    /**
     * @param GamePutResignRequest $resignRequest
     * @param GamePutResignRequest $resignError
     * @return \CoreBundle\Entity\Game
     */
    public function processPutResign(GamePutResignRequest $resignRequest, GamePutResignRequest $resignError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($resignRequest, $resignError);

        $game = $this->repository->find($resignRequest->getId());

        if (!$game instanceof Game) {
            $resignError->setId("Game is not found");
            $resignError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!in_array($me, [$game->getUserWhite(), $game->getUserBlack()])) {
            $resignError->setId("Game is not mine");
            $resignError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        switch (true) {
            case $me == $game->getUserBlack():
                $game->setResultWhite(1)->setResultBlack(0)->setStatus(GameStatus::END);
                break;
            case $me == $game->getUserWhite():
                $game->setResultWhite(0)->setResultBlack(1)->setStatus(GameStatus::END);
                break;
        }

        $this->manager->flush($game);

        return $this->getUserGame($me, $game);
    }

    /**
     * @param GamePutOfferdrawRequest $drawRequest
     * @param GamePutOfferdrawRequest $drawError
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $drawRequest, GamePutOfferdrawRequest $drawError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($drawRequest, $drawError);

        $game = $this->repository->find($drawRequest->getId());

        if (!$game instanceof Game) {
            $drawError->setId("Game is not found");
            $drawError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!in_array($me, [$game->getUserWhite(), $game->getUserBlack()])) {
            $drawError->setId("Game is not mine");
            $drawError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        switch (true) {
            case $me == $game->getUserBlack():
                $game->setDraw(GameColor::BLACK);
                break;
            case $me == $game->getUserWhite():
                $game->setDraw(GameColor::WHITE);
                break;
        }

        $this->manager->flush($game);

        return $this->getUserGame($me, $game);
    }

    /**
     * @param GamePutAcceptdrawRequest $drawRequest
     * @param GamePutAcceptdrawRequest $drawError
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $drawRequest, GamePutAcceptdrawRequest $drawError)
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($drawRequest, $drawError);

        $game = $this->repository->find($drawRequest->getId());

        if (!$game instanceof Game) {
            $drawError->setId("Game is not found");
            $drawError->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!in_array($me, [$game->getUserWhite(), $game->getUserBlack()])) {
            $drawError->setId("Game is not mine");
            $drawError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        switch (true) {
            case $me == $game->getUserBlack() && $game->getDraw() == GameColor::WHITE:
            case $me == $game->getUserWhite() && $game->getDraw() == GameColor::BLACK:
                $game->setResultWhite(0.5)->setResultBlack(0.5)->setStatus(GameStatus::END);
                break;
            default:
                $drawError->setId("Draw was not offered by opponent");
                $drawError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->flush($game);

        return $this->getUserGame($me, $game);
    }

    /**
     * @param User $user
     * @param null $status
     * @return \CoreBundle\Entity\Game[]
     */
    public function getGamesForUser(User $user, $status)
    {
        $games = $this->manager
            ->createQuery(
                "SELECT g FROM CoreBundle:Game g
                          WHERE (g.userWhite = :user OR g.userBlack = :user) AND g.status = :status
                          ORDER BY g.id ASC"
            )
            ->setParameter("status", $status)
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
     * @param int $gameId
     * @return Game
     */
    public function getUserGameByGameId(User $user, $gameId)
    {
        $game = $this->repository->find($gameId);

        if (!$game instanceof Game) {
            throw new GameHandlerException("Game is not found");
        }

        return $this->getUserGame($user, $game);
    }

    /**
     * @param int $gameId
     * @return Game
     */
    public function getGameByGameId($gameId)
    {
        return $this->repository->find($gameId);
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
                throw new GameHandlerException("Unknown user {$user->getLogin()} for game {$game->getId()}");
        }
    }

    /**
     * @param User $user
     * @param Game $game
     */
    public function defineUserMoveAndOpponentForGame(User $user, Game $game)
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
                throw new GameHandlerException("Unknown user {$user->getLogin()} for game {$game->getId()}");
        }
    }

    /**
     * @param User $me
     * @param User $opponent can be null - in this case common call will be created
     * @param $myColor
     * @return Game
     */
    public function createMyGame(User $me, User $opponent, $myColor)
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
                throw new GameHandlerException("Color is incorrect");
        }

        $game->setStatus(GameStatus::CALL)
            ->setTimeLastMove(new \DateTime())
            ->setUserToMove($game->getUserWhite());

        $this->container->get("core.handler.game")->defineUserColorForGame($me, $game);

        return $game;
    }

    /**
     * @param User $user
     * @param Game $game
     * @return Game
     */
    public function getUserGame(User $user, Game $game)
    {
        $this->defineUserColorForGame($user, $game);
        $this->defineUserMoveAndOpponentForGame($user, $game);

        return $game;
    }
}