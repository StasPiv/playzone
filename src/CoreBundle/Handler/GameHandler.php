<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:59
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\ChatMessage;
use CoreBundle\Exception\Handler\GameHandlerException;
use CoreBundle\Exception\Handler\User\UserHandlerException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\ChatMessage\ChatMessageType;
use CoreBundle\Model\Game\GameMove;
use CoreBundle\Entity\GameMove as GameMoveEntity;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GameGetRobotmoveAction;
use CoreBundle\Model\Request\Game\GamePostAddmessageRequest;
use CoreBundle\Model\Request\Game\GamePostAddmoveRequest;
use CoreBundle\Model\Request\Game\GamePostNewrobotRequest;
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
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use CoreBundle\Entity\User;

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
        $game = $this->repository->find($gameRequest->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$gameRequest->getLogin()) {
            return $game;
        }

        $user = $this->container->get("core.handler.user")
                     ->getUserByLoginAndToken($gameRequest->getLogin(), $gameRequest->getToken());

        return $this->getUserGame($game, $user);
    }

    /**
     * @param GameGetRobotmoveAction $request
     * @return GameMove
     */
    public function processGetRobotmove(GameGetRobotmoveAction $request) : GameMove
    {
        $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
            $request, $this->getRequestError()
        );

        $fen = base64_decode($request->getEncodedFen());

        $moveString = $this->container->get("core.service.chess")->getBestMoveFromFen($fen);

        return (new GameMove())->setFrom(substr($moveString, 0, 2))
                               ->setTo(substr($moveString, 2, 2));
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
        
        $game->setStatus(GameStatus::PLAY);

        $game->setTimeWhite($request->getTime()->getBase())
             ->setTimeBlack($request->getTime()->getBase());
        
        $this->defineUserColorForGame($me, $game);
        $this->defineUserMoveAndOpponentForGame($me, $game);

        $this->manager->persist($game);

        $this->manager->flush();

        return $game;
    }

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $pgnRequest) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($pgnRequest, $this->getRequestError());

        $game = $this->repository->find($pgnRequest->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($game->getStatus() !== GameStatus::PLAY) {
            $this->getRequestError()->addError("id", "Game is not played")
                                    ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if (
            $me != $game->getUserToMove() &&
            !in_array(0, [$pgnRequest->getTimeBlack(), $pgnRequest->getTimeWhite()]) &&
            !in_array("Robot", [$game->getUserWhite(), $game->getUserBlack()])
        ) {
            $this->getRequestError()->addError("pgn", "It is not your turn")
                                    ->throwException(ResponseStatusCode::BAD_FORMAT);
        }

        $pgn = $this->container->get("core.service.chess")->decodePgn($pgnRequest->getPgn());

        if (!$this->container->get("core.service.chess")->isValidPgn($pgn)) {
            $this->getRequestError()->addError("pgn", "Pgn is incorrect");
            $this->getRequestError()->throwException(ResponseStatusCode::BAD_FORMAT);
        }

        $game->setTimeWhite((int)$pgnRequest->getTimeWhite())
             ->setTimeBlack((int)$pgnRequest->getTimeBlack());

        if ($pgn !== $game->getPgn()) {
            $game->setDraw("")
                 ->setPgn($pgn);

            if (in_array("Robot", [$game->getUserWhite(), $game->getUserBlack()])) {
                $game->setUserToMove(
                    $game->getUserToMove() == $game->getUserWhite() ?
                        $game->getUserBlack() : $game->getUserWhite()
                );
            } else {
                $game->setUserToMove(
                    $me == $game->getUserWhite() ? $game->getUserBlack() : $game->getUserWhite()
                );
            }
        }

        $game->setInsufficientMaterialWhite($pgnRequest->isInsufficientMaterialWhite())
             ->setInsufficientMaterialBlack($pgnRequest->isInsufficientMaterialBlack());
        
        $this->fixResultIfTimeOver($pgnRequest, $game);

        if ($this->container->get("core.service.chess")->isGameInCheckmate($game->getPgn())) {
            $game->setResultWhite((int)($game->getUserToMove() === $game->getUserBlack()))
                 ->setResultBlack((int)($game->getUserToMove() === $game->getUserWhite()))
                 ->setStatus(GameStatus::END);
        }

        $this->manager->flush($game);

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePutResignRequest $resignRequest
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $resignRequest) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($resignRequest, $this->getRequestError());

        $game = $this->repository->find($resignRequest->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
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

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePutOfferdrawRequest $drawRequest
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $drawRequest) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($drawRequest, $this->getRequestError());

        $game = $this->repository->find($drawRequest->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
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
     * @param GamePutAcceptdrawRequest $drawRequest
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $drawRequest) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($drawRequest, $this->getRequestError());

        $game = $this->repository->find($drawRequest->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        if (!$this->isMyGame($game, $me)) {
            $this->getRequestError()->addError("id", "Game is not mine");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $gameAgainstRobot = in_array("Robot", [$game->getUserWhite(), $game->getUserBlack()]);
        
        switch (true) {
            case $me == $game->getUserBlack() && ($game->getDraw() == GameColor::WHITE || $gameAgainstRobot):
            case $me == $game->getUserWhite() && ($game->getDraw() == GameColor::BLACK || $gameAgainstRobot):
                $game->setResultWhite(0.5)->setResultBlack(0.5)->setStatus(GameStatus::END);
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

        $chatMessage = (new ChatMessage())->setMessage($request->getMessage())
                                          ->setUser($me)
                                          ->setType(ChatMessageType::GAME());


        $game->addChatMessage($chatMessage);

        $this->manager->persist($chatMessage);
        $this->manager->persist($game);

        $this->manager->flush();

        return $this->getUserGame($game, $me);
    }

    /**
     * @param GamePostAddmoveRequest $request
     * @return Game
     */
    public function processPostAddmove(GamePostAddmoveRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        $game = $this->repository->find($request->getId());

        if (!$game instanceof Game) {
            $this->getRequestError()->addError("id", "Game is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::NOT_FOUND);
        }

        $game->addMove(
            (new GameMoveEntity())
                ->setLag($request->getLag())
                ->setTime(new \DateTime())
        );

        $this->manager->persist($game);

        $this->manager->flush();

        return $this->getUserGame($game, $me);
    }

    /**
     * @param User $user
     * @param null $status
     * @param int $limit
     * @return \CoreBundle\Entity\Game[]
     */
    public function getGamesForUser(User $user, $status, $limit = null)
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

        return $this->getUserGame($game, $user);
    }

    /**
     * @param int $gameId
     * @param User $user
     * @return Game
     */
    public function getGameById($gameId, User $user = null)
    {
        $game = $this->repository->find($gameId);
        
        if (!$game instanceof Game) {
            return null;
        }
        
        return $this->getUserGame($game, $user);
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

        $game->setStatus(GameStatus::PLAY)
            ->setTimeLastMove(new \DateTime())
            ->setUserToMove($game->getUserWhite());

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
             ->setUserMove(false);

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
    private function isMyGame(Game $game, User $user)
    {
        return in_array($user, [$game->getUserWhite(), $game->getUserBlack()]);
    }

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @param Game $game
     */
    private function fixResultIfTimeOver(GamePutPgnRequest $pgnRequest, Game $game)
    {
        if ($pgnRequest->getTimeWhite() !== null && $game->getTimeWhite() <= 100) {
            switch ($pgnRequest->isInsufficientMaterialBlack()) {
                case false:
                    $game->setResultWhite(0)
                         ->setResultBlack(1)
                         ->setStatus(GameStatus::END);
                    break;
                case true:
                    $game->setResultWhite(0.5)
                         ->setResultBlack(0.5)
                         ->setStatus(GameStatus::END);
                    break;
            }
        } elseif ($pgnRequest->getTimeBlack() !== null && $game->getTimeBlack() <= 100) {
            switch ($pgnRequest->isInsufficientMaterialWhite()) {
                case false:
                    $game->setResultWhite(1)
                         ->setResultBlack(0)
                         ->setStatus(GameStatus::END);
                    break;
                case true:
                    $game->setResultWhite(0.5)
                         ->setResultBlack(0.5)
                         ->setStatus(GameStatus::END);
                    break;
            }
        }
    }
}