<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:54
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Tournament\TournamentGameShouldBeSkippedException;
use CoreBundle\Exception\Handler\Tournament\TournamentMissRoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentPlayerNotFoundException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Processor\TournamentProcessorInterface;
use CoreBundle\Repository\TournamentRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TournamentHandler
 * @package CoreBundle\Handler
 */
class TournamentHandler implements TournamentProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var TournamentRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Tournament');
    }

    /**
     * @return TournamentRepository
     */
    public function getRepository() : TournamentRepository
    {
        return $this->repository;
    }
    
    /**
     * @param TournamentGetListRequest $listRequest
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $listRequest) : array
    {
        $tournaments = $this->repository->findAll();

        if ($listRequest->getToken()) {
            $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
                $listRequest,
                $this->getRequestError()
            );

            foreach ($tournaments as $tournament) {
                $this->setMineToTournament($tournament, $user);
            }
        }

        return $tournaments;
    }

    /**
     * @param TournamentGetRequest $getRequest
     * @return Tournament
     */
    public function processGet(TournamentGetRequest $getRequest) : Tournament
    {
        try {
            $tournament = $this->repository->find($getRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        return $tournament;
    }

    /**
     * @param TournamentGetCurrentgameRequest $request
     * @return Game
     */
    public function processGetCurrentgame(TournamentGetCurrentgameRequest $request) : Game
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());

        try {
            $tournament = $this->repository->find($request->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        try {
            $this->searchTournamentPlayer($tournament, $me);
        } catch (TournamentPlayerNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "This is not your tournament")->throwException(ResponseStatusCode::FORBIDDEN);
        }

        try {
            $game = $this->getGameForCurrentRound($tournament, $me);
        } catch (TournamentMissRoundException $e) {
            $this->getRequestError()->addError("tournament_id", "You miss this round")->throwException(ResponseStatusCode::FORBIDDEN);
        }

        /** @var Game $game */
        return $game;
    }

    /**
     * @param TournamentPostRecordRequest $recordRequest
     * @return Tournament
     */
    public function processPostRecord(TournamentPostRecordRequest $recordRequest) : Tournament
    {
        $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
            $recordRequest,
            $this->getRequestError()
        );

        try {
            $tournament = $this->repository->find($recordRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                                    ->throwException(ResponseStatusCode::NOT_FOUND);
        }
        
        /** @var Tournament $tournament */
        $tournament->addPlayer($user);

        $this->manager->persist($tournament);
        $this->manager->flush();

        $this->setMineToTournament($tournament, $user);

        return $tournament;
    }

    /**
     * @param TournamentDeleteUnrecordRequest $unrecordRequest
     * @return Tournament
     */
    public function processDeleteUnrecord(TournamentDeleteUnrecordRequest $unrecordRequest) : Tournament
    {
        $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
            $unrecordRequest,
            $this->getRequestError()
        );

        try {
            $tournament = $this->repository->find($unrecordRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        try {
            $tournamentPlayer = $this->searchTournamentPlayer($tournament, $user);
        } catch (TournamentPlayerNotFoundException $e) {
            return $tournament;
        }

        $this->manager->remove($tournamentPlayer);

        $this->manager->persist($tournament);
        $this->manager->flush();

        $this->setMineToTournament($tournament, $user);

        return $tournament;
    }

    /**
     * @param Tournament $tournament
     * @param Game $game
     * @param int $round
     */
    public function addGameToTournament(Tournament $tournament, Game $game, int $round = 0)
    {
        $tournamentGame = (new TournamentGame())->setTournament($tournament)
                                                ->setGame($game)
                                                ->setRound($round);
        
        $this->manager->persist($tournamentGame);
    }

    /**
     * @param Tournament $tournament
     * @return array|\CoreBundle\Entity\TournamentPlayer[]
     * @throws \Exception
     */
    public function getPlayers(Tournament $tournament) : array
    {
        return $this->container->get("doctrine")->getRepository("CoreBundle:TournamentPlayer")
            ->findBy(["tournament" => $tournament], ["id" => "ASC"]);
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return array|TournamentGame[]
     * @throws \Exception
     */
    public function getRoundGames(Tournament $tournament, int $round) : array
    {
        return $this->container->get("doctrine")->getRepository("CoreBundle:TournamentGame")
            ->findBy(
                [
                    "tournament" => $tournament,
                    "round" => $round
                ]
            );
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return TournamentPlayer
     * @throws \Exception
     */
    public function getTournamentPlayer(Tournament $tournament, User $user) : TournamentPlayer
    {
        $tournamentPlayer = $this->container->get("doctrine")
            ->getRepository("CoreBundle:TournamentPlayer")
            ->findOneBy(
                [
                    "player" => $user,
                    "tournament" => $tournament
                ]
            );

        if (!$tournamentPlayer instanceof TournamentPlayer) {
            throw new TournamentPlayerNotFoundException;
        }

        return $tournamentPlayer;
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function getUserPreferColor(Tournament $tournament, User $user)
    {
        return $this->getTournamentPlayer($tournament, $user)
                    ->getRequiredColor();
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @param TournamentPlayer $firstPlayer
     * @param TournamentPlayer $secondPlayer
     * @return TournamentGame
     */
    public function createTournamentGame(Tournament $tournament, int $round, TournamentPlayer $firstPlayer, TournamentPlayer $secondPlayer) : TournamentGame
    {
        if ($firstPlayer->getId() === 0 || $secondPlayer->getId() === 0) {
            throw new TournamentGameShouldBeSkippedException;
        }

        $game = new Game();

        switch (true) {
            case $firstPlayer->getRequiredColor() == GameColor::BLACK ||
                $secondPlayer->getRequiredColor() == GameColor::WHITE:
                $game->setUserWhite($secondPlayer->getPlayer())
                    ->setUserBlack($firstPlayer->getPlayer());
                break;
            default:
                $game->setUserWhite($firstPlayer->getPlayer())
                    ->setUserBlack($secondPlayer->getPlayer());
        }

        $game->setUserToMove($game->getUserWhite())
             ->setStatus(GameStatus::PLAY);

        $tournamentGame = new TournamentGame();

        $tournamentGame->setGame($game)
            ->setTournament($tournament)
            ->setRound($round);
        
        return $tournamentGame;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return void
     */
    public function clearRound(Tournament $tournament, int $round)
    {
        $existingTournamentGames = $this->manager->getRepository("CoreBundle:TournamentGame")
            ->findBy(
                [
                    "round" => $round,
                    "tournament" => $tournament
                ]
            );

        foreach ($existingTournamentGames as $exTournamentGame) {
            $this->manager->remove($exTournamentGame);
        }
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     */
    private function setMineToTournament(Tournament $tournament, User $user)
    {
        try {
            $this->searchTournamentPlayer($tournament, $user);
            $tournament->setMine(true);
        } catch (TournamentPlayerNotFoundException $e) {
            $tournament->setMine(false);
        }
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return TournamentPlayer
     */
    private function searchTournamentPlayer(Tournament $tournament, User $user) : TournamentPlayer
    {
        return $this->manager->getRepository("CoreBundle:TournamentPlayer")
                    ->findByTournamentAndUser($tournament, $user);
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return Game
     * @throws TournamentMissRoundException
     */
    private function getGameForCurrentRound(Tournament $tournament, User $user) : Game
    {
        $tournamentGames = $this->manager->getRepository("CoreBundle:TournamentGame")
                               ->findBy([
                                   "tournament" => $tournament,
                                   "round" => $tournament->getCurrentRound()
                               ]);

        foreach ($tournamentGames as $tournamentGame) {
            if ($this->container->get("core.handler.game")->isMyGame($tournamentGame->getGame(), $user)) {
                return $tournamentGame->getGame();
            }
        }

        throw new TournamentMissRoundException;
    }

}