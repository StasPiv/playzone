<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:59
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\ChatMessage;
use CoreBundle\Exception\Handler\Game\GameNotFoundException;
use CoreBundle\Exception\Handler\GameHandlerException;
use CoreBundle\Exception\Handler\Tournament\TournamentGameNotFoundException;
use CoreBundle\Exception\Handler\User\UserHandlerException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\ChatMessage\ChatMessageType;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Event\Game\GamePublishEvent;
use CoreBundle\Model\Game\GameMove;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GameGetRobotmoveAction;
use CoreBundle\Model\Request\Game\GamePostAddmessageRequest;
use CoreBundle\Model\Request\Game\GamePostNewrobotRequest;
use CoreBundle\Model\Request\Game\GamePostPublishRequest;
use CoreBundle\Model\Request\Game\GamePutAbortRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutFix;
use CoreBundle\Model\Request\Game\GamePutFixRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;
use CoreBundle\Model\Request\Game\GameFixResultInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\Game;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\User\UserType;
use CoreBundle\Processor\GameProcessorInterface;
use CoreBundle\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use CoreBundle\Entity\User;

/**
 * Class GameHandler
 * @package CoreBundle\Handler
 */
class GameHandler implements GameProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

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
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Game');
    }

    /**
     * @return GameRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param GameGetListRequest $listRequest
     * @return array|Game[]
     */
    public function processGetList(GameGetListRequest $listRequest) : array
    {
        switch ($listRequest->getUser()) {
            case UserType::ME:
                return $this->getGamesForUser(
                    $this->container->get("core.service.security")->getUserIfCredentialsIsOk($listRequest,
                        $this->getRequestError()), $listRequest->getStatus());
            case UserType::ALL:
                return $this->repository->findBy(['status' => $listRequest->getStatus()], ["id" => "ASC"]);
            default:
                try {
                    $user = $this->container->get("core.handler.user")->getRepository()->find($listRequest->getUser());
                } catch (UserNotFoundException $e) {
                    $this->getRequestError()->addError("user", "User not found")
                                            ->throwException(ResponseStatusCode::NOT_FOUND);
                }

                /** @var User $user */
                return $this->getGamesForUser(
                    $user, $listRequest->getStatus(), $listRequest->getLimit()
                );
        }
    }

    /**
     * @param GameGetRequest $gameRequest
     * @return Game
     */
    public function processGet(GameGetRequest $gameRequest) : Game
    {
        try {
            $game = $this->repository->find($gameRequest->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                                    ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$gameRequest->getLogin()) {
            return $game;
        }

        $user = $this->container->get("core.handler.user")
                     ->getUserByLoginAndToken($gameRequest->getLogin(), $gameRequest->getToken());

        return $this->getUserGame($game, $user);
    }

    /**
     * @param GamePostNewrobotRequest $request
     * @return Game
     */
    public function processPostNewrobot(GamePostNewrobotRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        $game = $this->container->get("core.handler.game")->createMyGame(
            $me,
            $this->manager->getRepository("CoreBundle:User")->findOneByLogin("Robot"),
            $request->getColor()
        );
        
        $this->changeGameStatus($game, GameStatus::PLAY);

        $game->setTimeWhite($request->getTime()->getBase())
             ->setTimeBlack($request->getTime()->getBase());
        
        $this->defineUserColorForGame($me, $game);
        $this->defineUserMoveAndOpponentForGame($me, $game);

        $this->saveEntity($game);

        return $game;
    }

    /**
     * @param GamePutPgnRequest $request
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                                    ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $game->setTimeWhite((int)$request->getTimeWhite())
             ->setTimeBlack((int)$request->getTimeBlack());

        if ($request->getCurrentMove() < $game->getCurrentMove()) {
            $this->manager->flush($game);
            return $this->getUserGame($game, $me);
        }

        if ($request->getCurrentMove() > $game->getCurrentMove() + 1) {
            sleep(1);
        }

        $pgn = $this->container->get("core.service.chess")->decodePgn($request->getPgn());

        if ($this->container->get("core.service.chess")->isValidPgn($pgn)) {
            $game->setPgn($pgn)
                 ->setUserToMove(
                     $me == $game->getUserWhite() ? $game->getUserBlack() : $game->getUserWhite()
            )->setCurrentMove($request->getCurrentMove());
        }

        $game->setDraw("")
             ->setTimeLastMove(new \DateTime());

        $game->setInsufficientMaterialWhite($request->isInsufficientMaterialWhite())
             ->setInsufficientMaterialBlack($request->isInsufficientMaterialBlack());
        
        $this->fixResultIfTimeOver($request, $game);
        
        if ($this->container->get("core.service.chess")->fixResult($game)) {
            $this->changeGameStatus($game, GameStatus::END);
        }

        $this->manager->flush($game);

        return $this->getUserGame($game, $me);
    }

    /**
     * @inheritDoc
     */
    public function processPostPublish(GamePostPublishRequest $request) : Game
    {
        $user = $this->container->get("core.handler.user")->getSecureUser($request);
        
        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        $gamePublishEvent = new GamePublishEvent();
        $gamePublishEvent->setGame($game);
        $gamePublishEvent->setUser($user);
        
        switch (true) {
            case !empty($request->getFen()):
                $this->container->get("event_dispatcher")->dispatch(
                    GameEvents::PUBLISH_FEN,
                    $gamePublishEvent->setFen($request->getFen())
                );
                break;
            case !empty($request->getPgn()):
                $this->container->get("event_dispatcher")->dispatch(
                    GameEvents::PUBLISH_PGN,
                    $gamePublishEvent->setPgn($request->getPgn())
                );
                break;
        }

        return $game;
    }

    /**
     * @param GamePutResignRequest $request
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        switch (true) {
            case $me == $game->getUserBlack():
                $game->setResultWhite(1)->setResultBlack(0);
                $this->changeGameStatus($game, GameStatus::END);
                break;
            case $me == $game->getUserWhite():
                $game->setResultWhite(0)->setResultBlack(1);
                $this->changeGameStatus($game, GameStatus::END);
                break;
        }

        $this->manager->flush($game);

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePutOfferdrawRequest $request
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                ->throwException(ResponseStatusCode::FORBIDDEN);
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

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePutAcceptdrawRequest $request
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $gameAgainstRobot = in_array("Robot", [$game->getUserWhite(), $game->getUserBlack()]);
        
        switch (true) {
            case $me == $game->getUserBlack() && ($game->getDraw() == GameColor::WHITE || $gameAgainstRobot):
            case $me == $game->getUserWhite() && ($game->getDraw() == GameColor::BLACK || $gameAgainstRobot):
                $game->setResultWhite(0.5)->setResultBlack(0.5);
                $this->changeGameStatus($game, GameStatus::END);
                break;
            default:
                $this->getRequestError()->addError("id", "Draw was not offered by opponent");
                $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->manager->flush($game);

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePostAddmessageRequest $request
     * @return Game
     */
    public function processPostAddmessage(GamePostAddmessageRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        $game = $this->repository->find($request->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        $chatMessage = $this->container->get("core.handler.chat")->createEntity()
                                        ->setMessage($request->getMessage())
                                        ->setUser($me)
                                        ->setType(ChatMessageType::GAME());


        $game->addChatMessage($chatMessage);

        $this->manager->persist($chatMessage);
        $this->saveEntity($game);

        return $this->getUserGame($game, $me);
    }

    /**
     * @param User $user
     * @param null $status
     * @param int $limit
     * @return Game[]
     */
    public function getGamesForUser(User $user, $status, $limit = null): array
    {
        $gamesQuery = $this->manager
            ->createQuery(
                "SELECT g FROM CoreBundle:Game g
                          WHERE (g.userWhite = :user OR g.userBlack = :user) AND g.status = :status
                          ORDER BY g.id DESC"
            )
            ->setParameter("status", $status)
            ->setParameter("user", $user);

        if ($limit) {
            $gamesQuery->setMaxResults($limit);
        }

        $games = $gamesQuery->getResult();

        foreach ($games as $game) {
            $this->defineUserColorForGame($user, $game);
            $this->defineUserMoveAndOpponentForGame($user, $game);
        }

        return $games;
    }

    /**
     * @param int $gameId
     * @param User $user
     * @return Game
     */
    public function getUserGameById(int $gameId, User $user = null) : Game
    {
        return $this->getUserGame($this->repository->find($gameId), $user);
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
     * @param bool $rate
     * @return Game
     */
    public function createMyGame(User $me, User $opponent, $myColor, bool $rate = true)
    {
        $game = $this->createEntity();

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

        $game->setTimeLastMove(new \DateTime())
            ->setUserToMove($game->getUserWhite())
            ->setRate($rate)
            ->setRatingWhite($game->getUserWhite()->getRating())
            ->setRatingBlack($game->getUserBlack()->getRating());

        $this->container->get("logger")->error("Switch move: " . $game->getUserToMove());
        
        $this->changeGameStatus($game, GameStatus::PLAY);

        $this->container->get("core.handler.game")->defineUserColorForGame($me, $game);
        
        $game->getUserWhite()->setLastColor(GameColor::WHITE);
        $game->getUserBlack()->setLastColor(GameColor::BLACK);

        return $game;
    }

    /**
     * @param Game $game
     * @param User $user
     * @return Game
     */
    public function getUserGame(Game $game, User $user = null)
    {
        $game->setMine(false)
             ->setUserMove(false)
             ->setCanAbort($this->container->get('core.service.chess')->canAbort($game));

        try {
            $game->getUserWhite()->setRating($game->getRatingWhite());
            $game->getUserBlack()->setRating($game->getRatingBlack());
        } catch (\Throwable $e) {
            $this->container->get('logger')->warning($e->getMessage());
        }

        if (!$user instanceof User || !$this->isMyGame($game, $user)) {
            return $game;
        }

        $game->setMine(true);
        $this->defineUserColorForGame($user, $game);
        $this->defineUserMoveAndOpponentForGame($user, $game);

        if ($game->getStatus() == GameStatus::END) {
            switch ($game->getColor()) {
                case GameColor::WHITE:
                    $game->setMyResult($game->getResultWhite());
                    break;
                case GameColor::BLACK:
                    $game->setMyResult($game->getResultBlack());
                    break;
            }
        }

        return $game;
    }

    /**
     * @param Game $game
     * @param User $user
     * @return bool
     */
    public function isMyGame(Game $game, User $user)
    {
        return in_array($user, [$game->getUserWhite(), $game->getUserBlack()]);
    }

    /**
     * @param GameFixResultInterface $request
     * @param Game $game
     */
    private function fixResultIfTimeOver(GameFixResultInterface $request, Game $game)
    {
        $gameService = $this->container->get("core.service.chess.game");
        $gameService->setPgn($game->getPgn());

        if ($request->getTimeWhite() !== null && $game->getTimeWhite() <= 100) {
            switch ($gameService->isInsufficientMaterialBlack()) {
                case false:
                    $game->setResultWhite(0)
                         ->setResultBlack(1);
                    $this->changeGameStatus($game, GameStatus::END);
                    break;
                case true:
                    $game->setResultWhite(0.5)
                         ->setResultBlack(0.5);
                    $this->changeGameStatus($game, GameStatus::END);
                    break;
            }
        } elseif ($request->getTimeBlack() !== null && $game->getTimeBlack() <= 100) {
            switch ($gameService->isInsufficientMaterialWhite()) {
                case false:
                    $game->setResultWhite(1)
                         ->setResultBlack(0);
                    $this->changeGameStatus($game, GameStatus::END);
                    break;
                case true:
                    $game->setResultWhite(0.5)
                         ->setResultBlack(0.5);
                    $this->changeGameStatus($game, GameStatus::END);
                    break;
            }
        }
    }

    /**
     * @param int $gameId
     * @param string $status
     * @param float $resultWhite
     * @param float $resultBlack
     */
    public function changeGameStatusById(int $gameId, string $status, float $resultWhite, float $resultBlack)
    {
        $game = $this->repository->find($gameId);

        $game->setResultWhite($resultWhite);
        $game->setResultBlack($resultBlack);

        $this->changeGameStatus($game, $status);
        $this->manager->flush($game);
    }

    /**
     * @param Game $game
     * @param $status
     */
    public function changeGameStatus(Game $game, $status)
    {
        $this->container->get("logger")->debug(__METHOD__ . ' ' . $game->getId() . ' ' . $status);
        $game->setStatus($status);

        $this->container->get("event_dispatcher")->dispatch(
            GameEvents::CHANGE_STATUS_BEFORE,
            (new GameEvent())->setGame($game)
        );

        $this->saveEntity($game);

        $this->container->get("event_dispatcher")->dispatch(
            GameEvents::CHANGE_STATUS_AFTER,
            (new GameEvent())->setGame($game)
        );
    }

    /**
     * @return Game
     */
    public function createEntity()
    {
        return (new Game())->setTimeLastMove(new \DateTime());
    }

    public function fixResultGames()
    {
        $this->fixResultGamesByColor("White");
        $this->fixResultGamesByColor("Black");

        $this->manager->flush();
    }

    /**
     * @param string $color "White" or "Black"
     */
    private function fixResultGamesByColor(string $color)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder("g");
        /** @var Game[] $games */
        $games = $queryBuilder
            ->where("g.status = :status")
            ->andWhere("g.userToMove = g.user{$color}")
            ->andWhere(
                "TIMESTAMPDIFF(SECOND, g.timeLastMove, CURRENT_TIMESTAMP()) > 60 + g.time{$color} / 1000"
            )
            ->setParameter("status", GameStatus::PLAY)
            ->innerJoin("CoreBundle:User", "u", "WITH", "u.id = g.user{$color}")
            ->getQuery()
            ->getResult();

        $this->container->get("logger")->info(
            "Remove games: " . $queryBuilder->getQuery()->getSQL()
        );

        foreach ($games as $game) {
            try {
                $this->container->get('core.handler.tournament')->getTournamentGameByGame($game);
                continue;
            } catch (TournamentGameNotFoundException $e) {
                // that's ok
            }

            switch ($color) {
                case "White":
                    $game->setResultWhite(0)->setResultBlack(1);
                    break;
                case "Black";
                    $game->setResultWhite(1)->setResultBlack(0);
                    break;
            }
            $this->changeGameStatus($game, GameStatus::END);
            $this->manager->persist($game);
        }
    }

    /**
     * @param Game $game
     */
    public function saveEntity(Game $game)
    {
        $this->manager->persist($game);
        $this->manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function processPutFix(GamePutFixRequest $request) : Game
    {
        $me = $this->container->get("core.handler.user")->getSecureUser($request);

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $game->setTimeWhite((int)$request->getTimeWhite())
             ->setTimeBlack((int)$request->getTimeBlack());
        
        $this->fixResultIfTimeOver($request, $game);

        $pgn = $this->container->get("core.service.chess")->decodePgn($request->getPgn());

        if ($this->container->get("core.service.chess")->isValidPgn($pgn)) {
            $game->setPgn($pgn);
            if ($this->container->get("core.service.chess")->fixResult($game)) {
                $this->changeGameStatus($game, GameStatus::END);
            }
        }

        /** @var Game $game */
        return $this->getUserGame($game, $me);
    }

    /**
     * @inheritDoc
     */
    public function processPutAbort(GamePutAbortRequest $request) : Game
    {
        $me = $this->container->get("core.handler.user")->getSecureUser($request);

        try {
            $game = $this->repository->find($request->getId());
        } catch (GameNotFoundException $e) {
            $this->getRequestError()->addError("id", "Game is not found")
                ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Game $game */
        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        try {
            $this->container->get("core.handler.tournament")->getTournamentForGame($game);
            $this->getRequestError()->addError("id", "This is tournament game")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (TournamentGameNotFoundException $e) {
            // it's ok
        }

        /** @var Game $game */
        if (!$this->container->get("core.service.chess")->canAbort($game)) {
            $this->getRequestError()->addError("id", "Game is too long")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $game->setResultWhite(0)->setResultBlack(0)->setStatus(GameStatus::ABORTED);

        $this->saveEntity($game);

        return $this->getUserGame($game, $me);
    }
}