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
use CoreBundle\Exception\Handler\Tournament\TournamentGameNotFoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentGameShouldBeSkippedException;
use CoreBundle\Exception\Handler\Tournament\TournamentMissRoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentPlayerNotFoundException;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Event\Tournament\TournamentInitializator;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentStatus;
use CoreBundle\Model\Tournament\TournamentType;
use CoreBundle\Processor\TournamentProcessorInterface;
use CoreBundle\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TournamentHandler
 * @package CoreBundle\Handler
 */
class TournamentHandler implements TournamentProcessorInterface, EventSubscriberInterface
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
     * @param TournamentGetListRequest $request
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $request) : array
    {
        $tournaments = $this->repository
            ->findBy(['status' => $request->getStatus()], ['id' => 'DESC'], $request->getLimit());

        if ($request->getToken()) {
            $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
                $request,
                $this->getRequestError()
            );

            foreach ($tournaments as $tournament) {
                $tournament->setGames(new ArrayCollection());
                $tournament->setPlayers(new ArrayCollection());
                $this->setMineToTournament($tournament, $user);
            }
        }

        return $tournaments;
    }

    /**
     * @param TournamentGetRequest $request
     * @return Tournament
     */
    public function processGet(TournamentGetRequest $request) : Tournament
    {
        try {
            $tournament = $this->repository->find($request->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        $this->container->get("core.service.tournament_table.factory")
             ->create($tournament->getTournamentParams()->getType())
             ->mixTournamentTable($tournament);

        try {
            $this->setMineToTournament(
                $tournament,
                $this->container->get("core.handler.user")->getSecureUser($request)
            );
        } catch (\Exception $e) {
            // it's ok
        }

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
        return $this->container->get('core.handler.game')->getUserGame($game, $me);
    }

    /**
     * @param TournamentPostRecordRequest $request
     * @return Tournament
     */
    public function processPostRecord(TournamentPostRecordRequest $request) : Tournament
    {
        $user = $this->container->get("core.handler.user")->getSecureUser($request);

        try {
            $tournament = $this->repository->find($request->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                                    ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        if ($tournament->isPrivate()) {
            $this->getRequestError()->addError("login", "This is private tournament")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($tournament->getStatus() != TournamentStatus::NEW()) {
            $this->getRequestError()->addError("login", "Tournament has already been started")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($user->getLag() > $this->container->getParameter("max_lag_for_record")) {
//            $this->getRequestError()->addError("login", "Your lag is too big")
//                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($user->isBanned()) {
            $this->getRequestError()->addError("login", "You are banned")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        if ($this->isUserInTournament($tournament, $user)) {
            $this->getRequestError()->addError("login", "You are already in tournament")
                ->throwException(ResponseStatusCode::FORBIDDEN);
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
     * @param TournamentPlayer $white
     * @param TournamentPlayer $black
     */
    public function addGameToTournament(
        Tournament $tournament,
        Game $game,
        int $round = 0,
        TournamentPlayer $white,
        TournamentPlayer $black
    )
    {
        $tournamentGame = (new TournamentGame())->setTournament($tournament)
                                                ->setGame($game)
                                                ->setRound($round)
                                                ->setPlayerWhite($white)
                                                ->setPlayerBlack($black);
        
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
     * @return bool
     */
    public function isCurrentRoundFinished(Tournament $tournament) : bool
    {
        foreach ($this->getRoundGames($tournament, $tournament->getCurrentRound()) as $tournamentGame) {
            if (!$this->isGameFinished($tournamentGame->getGame())) {
                return false;
            }
        }

        return true;
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

        $game = $this->container->get("core.handler.game")->createEntity();

        switch (true) {
            case $firstPlayer->getRequiredColor() == GameColor::BLACK ||
                $secondPlayer->getRequiredColor() == GameColor::WHITE:
                $game->setUserWhite($secondPlayer->getPlayer())
                    ->setUserBlack($firstPlayer->getPlayer());
                $whiteTournamentPlayer = $secondPlayer;
                $blackTournamentPlayer = $firstPlayer;
                break;
            default:
                $game->setUserWhite($firstPlayer->getPlayer())
                    ->setUserBlack($secondPlayer->getPlayer());
                $whiteTournamentPlayer = $firstPlayer;
                $blackTournamentPlayer = $secondPlayer;
        }

        $game->setUserToMove($game->getUserWhite());
        $game->setTimeWhite($tournament->getGameParams()->getTimeBase())
             ->setTimeBlack($tournament->getGameParams()->getTimeBase())
             ->setRatingWhite($whiteTournamentPlayer->getPlayer()->getRating())
             ->setRatingBlack($blackTournamentPlayer->getPlayer()->getRating());

        $game->setGameParams($tournament->getGameParams());
        
        $this->container->get("core.handler.game")->changeGameStatus($game, GameStatus::PLAY);

        $tournamentGame = new TournamentGame();

        $tournamentGame->setGame($game)
                       ->setTournament($tournament)
                       ->setRound($round)
                       ->setPlayerWhite($whiteTournamentPlayer)
                       ->setPlayerBlack($blackTournamentPlayer);
        
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
     * @param Game $game
     * @return Tournament
     * @throws TournamentGameNotFoundException
     */
    public function getTournamentForGame(Game $game) : Tournament 
    {
        try {
            $tournamentGame = $this->manager->getRepository("CoreBundle:TournamentGame")
                ->findOneBy([
                    "game" => $game
                ]);
        } catch (\Exception $e) {
            throw new TournamentGameNotFoundException;
        }
        
        if (!$tournamentGame instanceof TournamentGame) {
            throw new TournamentGameNotFoundException;
        }
        
        return $tournamentGame->getTournament();
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

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TournamentEvents::START => [
                ['onTournamentStart', 20]    
            ],
            TournamentEvents::ROUND_START => [
                ['onTournamentRoundStart', 20]
            ],
            GameEvents::CHANGE_STATUS_AFTER => [
                ['onGameChangeStatus', 20]
            ],
            TournamentEvents::TOURNAMENT_FINISHED => [
                ['onTournamentFinished', 20]
            ]
        ];
    }

    /**
     * @param GameEvent $gameEvent
     */
    public function onGameChangeStatus(GameEvent $gameEvent)
    {
        if (!$this->isGameFinished($gameEvent->getGame())) {
            return;
        }

        try {
            $tournament = $this->container->get("core.handler.tournament")
                ->getTournamentForGame($gameEvent->getGame());
        } catch (TournamentGameNotFoundException $e) {
            return;
        }

        $this->updatePlayersTotals($gameEvent->getGame());

        if (!$this->container->get("core.handler.tournament")->isCurrentRoundFinished($tournament)) {
            return;
        }
        
        $this->container->get("event_dispatcher")->dispatch(
            TournamentEvents::ROUND_FINISHED,
            (new TournamentContainer())->setTournament($tournament)
        );
        
        $this->recalculatePointsAndCoefficients($tournament);

        if ($tournament->getRounds() == $tournament->getCurrentRound()) {
            $this->changeTournamentStatus($tournament, TournamentStatus::END());

            $this->manager->persist($tournament);
            $this->manager->flush();

            $this->container->get("event_dispatcher")->dispatch(
                TournamentEvents::TOURNAMENT_FINISHED,
                (new TournamentContainer())->setTournament(
                    // for correct players ordering
                    $this->manager->find('CoreBundle:Tournament', $tournament->getId())
                )
            );
        }
        
    }

    /**
     * @param TournamentContainer $event
     */
    public function onTournamentFinished(TournamentContainer $event)
    {
        $event->getTournament()->setStatus(TournamentStatus::END());

        $this->manager->persist($event->getTournament());

        $this->manager->flush();
    }

    /**
     * @param TournamentContainer $tournamentContainer
     */
    public function onTournamentRoundStart(TournamentContainer $tournamentContainer)
    {
        $this->searchPlayerWhoMissCurrentRound($tournamentContainer->getTournament());
    }

    /**
     * @param TournamentContainer $tournamentContainer
     */
    public function onTournamentStart(TournamentContainer $tournamentContainer)
    {
        $tournament = $tournamentContainer->getTournament();

        $this->removeOfflinePlayers($tournament);

        if ($tournament->getPlayers()->count() > $this->container->getParameter("app_max_players_for_round_robin")
        ) {
            $this->changeTournamentTypeOnSwiss($tournament);
        }

        if ($tournament->getTournamentParams()->getGamesVsOpponent() == 1) {
            switch (
            count($tournament->getPlayers())
            ) {
                case 0:
                case 1:
                    $this->manager->remove($tournament);
                    $this->manager->flush();

                    return;
                    break;
                case 2:
                    $tournament->getTournamentParams()->setGamesVsOpponent(4);
                    break;
                case 3:
                case 4:
                    $tournament->getTournamentParams()->setGamesVsOpponent(2);
                    break;
            }
        }

        $this->changeTournamentStatus($tournament, TournamentStatus::CURRENT());
        $this->calculateRounds($tournament);

        $this->manager->flush($tournament);
    }

    /**
     * @param Tournament $tournament
     */
    private function changeTournamentTypeOnSwiss(Tournament $tournament)
    {
        $tournamentParams = TournamentParamsFactory::create(TournamentType::SWITZ())
            ->setTimeBegin($tournament->getTournamentParams()->getTimeBegin());

        $tournament->setTournamentParams($tournamentParams)->setRounds(
            $this->container->getParameter("rounds_for_swiss")
        )->setRounds($this->container->get('core.service.swiss')->calculateRoundsForSwiss($tournament));

        $this->manager->persist($tournament);
        $this->manager->flush();
    }

    /**
     * @param Tournament $tournament
     */
    public function calculateRounds(Tournament $tournament)
    {
        if ($tournament->getRounds() != 0) {
            return;
        }

        $countPlayers = count($tournament->getPlayers());

        $tournament->setRounds(
            $tournament->getTournamentParams()->getGamesVsOpponent() *
            ($countPlayers % 2 === 0 ? $countPlayers - 1 : $countPlayers)
        );
    }

    /**
     * @param Game $game
     */
    private function updatePlayersTotals(Game $game)
    {
        $tournamentGame = $this->getTournamentGameByGame($game);

        $playerWhite = $tournamentGame->getPlayerWhite();
        $playerBlack = $tournamentGame->getPlayerBlack();

        $playerWhite->setPoints($playerWhite->getPoints() + $game->getResultWhite());
        $playerBlack->setPoints($playerBlack->getPoints() + $game->getResultBlack());

        $playerWhite->setBlackInRow(0)->setWhiteInRow($playerWhite->getWhiteInRow() + 1);
        $playerBlack->setBlackInRow($playerBlack->getBlackInRow() + 1)->setWhiteInRow(0);

        $playerWhite->setCountWhite($playerWhite->getCountWhite() + 1);
        $playerBlack->setCountBlack($playerBlack->getCountBlack() + 1);

        $playerWhite->addOpponent($playerBlack->getPlayer());
        $playerBlack->addOpponent($playerWhite->getPlayer());
        
        $this->updateCoefficients($playerWhite, $playerBlack, $game->getResultWhite());
        $this->updateCoefficients($playerBlack, $playerWhite, $game->getResultBlack());

        $this->manager->persist($playerWhite);
        $this->manager->persist($playerBlack);

        $this->manager->flush();
    }

    /**
     * @param Game $game
     * @return TournamentGame
     * @throws TournamentGameNotFoundException
     */
    public function getTournamentGameByGame(Game $game) : TournamentGame
    {
        $tournamentGame = $this->manager->getRepository("CoreBundle:TournamentGame")
            ->findOneBy([
                "game" => $game
            ]);
        
        if (!$tournamentGame instanceof TournamentGame) {
            throw new TournamentGameNotFoundException;
        }
        
        return $tournamentGame;
    }

    /**
     * @param string $frequency
     * @param string $timeBegin
     * @param string $tournamentName
     * @param int $timeBase
     * @param int $timeIncrement
     * @param bool $private
     * @param array|string $players
     * @param int $gamesVsOpponent
     */
    public function createTournamentEvent(
        string $frequency,
        string $timeBegin,
        string $tournamentName,
        int $timeBase,
        int $timeIncrement = 0,
        bool $private = false,
        string $players = '',
        int $gamesVsOpponent = 2
    )
    {
        $this->container->get("core.handler.event")->initEventAndSave(
            (new TournamentInitializator())
                ->setFrequency($frequency)
                ->setPlayerIds(empty($players) ? [] : explode(',', $players))
                ->setTournamentName($tournamentName)
                ->setPrivate($private)
                ->setGameParams(
                    (new GameParams())->setTimeBase($timeBase)->setTimeIncrement($timeIncrement)
                )
                ->setTimeBegin($timeBegin)
                ->setTournamentParams(
                    TournamentParamsFactory::create(TournamentType::ROUND_ROBIN())
                        ->setGamesVsOpponent($gamesVsOpponent)
                )
            , "core.service.event.tournament.create"
        );
    }

    /**
     * @param int $tournamentId
     * @param string $playersIdsString
     */
    public function addPlayersIntoTournament(int $tournamentId, string $playersIdsString)
    {
        $tournament = $this->getRepository()->find($tournamentId);

        if ($tournament->getCurrentRound() != 0 || $tournament->isPrivate()) {
            return;
        }

        $playerIds = explode(',', $playersIdsString);

        foreach ($playerIds as $playerId) {
            try {
                $tournament->addPlayer(
                    $this->container->get('core.handler.user')->getRepository()->find($playerId)
                );
            } catch (\Exception $e) {
                $this->container->get('logger')->error($e->getMessage());
            }
        }

        $this->manager->flush($tournament);
    }

    /**
     * @param Tournament $tournament
     */
    private function searchPlayerWhoMissCurrentRound(Tournament $tournament)
    {
        $roundGames = $this->getRoundGames($tournament, $tournament->getCurrentRound());
        $players = $this->getPlayers($tournament);

        $playersForCurrentRound = [];
        foreach ($roundGames as $game) {
            $playersForCurrentRound[$game->getPlayerWhite()->getId()] = $game->getPlayerWhite()->getId();
            $playersForCurrentRound[$game->getPlayerBlack()->getId()] = $game->getPlayerBlack()->getId();
        }

        foreach ($players as $player) {
            if (!isset($playersForCurrentRound[$player->getId()])) {
                $player->setMissedRound(true);
                $this->manager->persist($player);
            }
        }

        $this->manager->flush();
    }

    /**
     * @param Tournament $tournament
     */
    public function removeOfflinePlayers(Tournament $tournament)
    {
        $offlinePlayers = $tournament->getPlayers()->filter(
            function (TournamentPlayer $tournamentPlayer) {
                return !$tournamentPlayer->getPlayer()->isOnline();
            }
        );

        foreach ($offlinePlayers as $player) {
            $tournament->getPlayers()->removeElement($player);
            $this->manager->remove($player);
        }

        $this->manager->flush();
    }

    /**
     * @param int $tournamentId
     */
    public function recalculatePointsAndCoefficientsById(int $tournamentId)
    {
        try {
            $tournament = $this->repository->find($tournamentId);
        } catch (TournamentNotFoundException $e) {
            $this->container->get("logger")->error("Tournament $tournamentId is not found");
            return;
        }
        
        $this->recalculatePointsAndCoefficients($tournament);
    }

    /**
     * @param Tournament $tournament
     */
    private function recalculatePointsAndCoefficients(Tournament $tournament)
    {
        $this->createCalculatorInstance($tournament)->calculate($tournament);
    }

    /**
     * @param TournamentPlayer $player
     * @param TournamentPlayer $opponent
     * @param float $result
     */
    private function updateCoefficients(
        TournamentPlayer $player,
        TournamentPlayer $opponent,
        float $result
    ) {
        $this->createCalculatorInstance($player->getTournament())
             ->updateCoefficients($player, $opponent, $result);
    }

    /**
     * @param Tournament $tournament
     * @param TournamentStatus $status
     */
    public function changeTournamentStatus(Tournament $tournament, TournamentStatus $status)
    {
        $tournament->setStatus($status);

        $this->container->get("event_dispatcher")->dispatch(
            TournamentEvents::CHANGE_STATUS,
            (new TournamentContainer())->setTournament($tournament)
        );
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return bool
     */
    private function isUserInTournament(Tournament $tournament, User $user): bool
    {
        foreach ($tournament->getPlayers() as $tournamentPlayer) {
            if ($tournamentPlayer->getPlayer() == $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Tournament $tournament
     * @return \CoreBundle\Model\Tournament\TournamentCalculatorInterface
     */
    private function createCalculatorInstance(Tournament $tournament)
    {
        return $this->container->get("core.service.calculator.factory")->create($tournament);
    }

    /**
     * @param Game $game
     * @return bool
     */
    private function isGameFinished(Game $game)
    {
        return in_array($game->getStatus(), [GameStatus::END, GameStatus::ABORTED]);
    }
}