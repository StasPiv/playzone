<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.04.16
 * Time: 22:11
 */

namespace CoreBundle\Service\Tournament;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Exception\Handler\Tournament\TournamentDrawIncorrectException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Tournament\TournamentCalculatorInterface;
use CoreBundle\Model\Tournament\TournamentDrawInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SwissService
 * @package CoreBundle\Service
 */
class SwissService implements TournamentDrawInterface, TournamentCalculatorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var array
     */
    private $possibleOpponentsMap = [];

    /**
     * @var array|TournamentGame[]
     */
    private $possibleTournamentGames = [];

    /**
     * @var bool
     */
    private $existsMissedPlayer = false;

    /**
     * @var bool
     */
    private $ignoreColors = false;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return void
     */
    public function makeDraw(Tournament $tournament, int $round)
    {
        $connection = $this->manager->getConnection();

        $connection->beginTransaction();
        try {
            $this->processDraw($tournament, $round);
            $this->manager->flush();
            $connection->commit();
        } catch (TournamentDrawIncorrectException $e) {
            $connection->rollBack();
            $this->container->get("logger")->debug("DRAW no opponents: " . $e->getMessage());
            $this->makeDraw($tournament, $round);
        }
    }

    /**
     * @param Tournament $tournament
     * @return void
     */
    public function makeDrawForNextRound(Tournament $tournament)
    {
        $nextRound = $tournament->getCurrentRound() + 1;
        $this->makeDraw($tournament, $nextRound);
        $tournament->setCurrentRound($nextRound);
        $this->manager->flush($tournament);
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function processDraw(Tournament $tournament, int $round)
    {
        $this->clearRound($tournament, $round);

        $this->buildPossibleOpponentsMapForEachPlayer(
            $this->getSortedTournamentPlayers(
                $this->getTournamentHandler()->getPlayers($tournament)
            )
        );

        $this->createTournamentGames($tournament, $round);
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return void
     */
    private function clearRound(Tournament $tournament, int $round)
    {
        $this->getTournamentHandler()->clearRound($tournament, $round);

        foreach ($this->possibleTournamentGames as $possibleTournamentGame) {
            $this->manager->detach($possibleTournamentGame);
            $this->manager->detach($possibleTournamentGame->getGame());
        }
        
        $this->possibleTournamentGames = [];
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @param TournamentPlayer $firstPlayer
     * @param TournamentPlayer $secondPlayer
     * @return void
     */
    private function createTournamentGame(Tournament $tournament, int $round, TournamentPlayer $firstPlayer, TournamentPlayer $secondPlayer)
    {

        if ($this->calculateWhiteAvailabilty($firstPlayer) > $this->calculateWhiteAvailabilty($secondPlayer)) {
            $tournamentGame = $this->getTournamentHandler()->createTournamentGame(
                $tournament,
                $round,
                $firstPlayer,
                $secondPlayer
            );
        } else {
            $tournamentGame = $this->getTournamentHandler()->createTournamentGame(
                $tournament,
                $round,
                $secondPlayer,
                $firstPlayer
            );
        }

        $this->manager->persist($tournamentGame);

        $this->possibleTournamentGames[] = $tournamentGame;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function createTournamentGames(Tournament $tournament, int $round)
    {
        $this->createNewGamesFromPossibleOpponentsMap($tournament, $round);
    }

    /**
     * @param TournamentPlayer[] $tournamentPlayers
     * @return TournamentPlayer[]
     */
    private function getSortedTournamentPlayers(array $tournamentPlayers) : array
    {
        usort(
            $tournamentPlayers,
            function(TournamentPlayer $a, TournamentPlayer $b)
            {
                $pointsB = $b->getPoints() + $b->getPointsForDraw();
                $pointsA = $a->getPoints() + $a->getPointsForDraw();

                if ($pointsA === $pointsB) {
                    if ($b->getRequiredColor() == $a->getRequiredColor()) {
                        return $b->getPlayer()->getLogin() <=> $a->getPlayer()->getLogin();
                    }

                    return $b->getRequiredColor() <=> $a->getRequiredColor();
                }

                return $pointsB <=> $pointsA;
            }
        );

        return $tournamentPlayers;
    }

    /**
     * @param TournamentPlayer $tournamentPlayer
     * @return string
     */
    private function getPlayerInfo(TournamentPlayer $tournamentPlayer) : string
    {
        return $tournamentPlayer->getPlayer()->getLogin() .
               " (" . $tournamentPlayer->getPlayer()->getId() . ")" . " " .
               $this->container->get("jms_serializer")->serialize($tournamentPlayer, "json");
    }

    /**
     * @return TournamentHandler
     */
    private function getTournamentHandler() : TournamentHandler
    {
        return $this->container->get("core.handler.tournament");
    }

    /**
     * @param TournamentPlayer[] $players
     */
    private function buildPossibleOpponentsMapForEachPlayer(array $players)
    {
        $this->possibleOpponentsMap = [];
        $this->existsMissedPlayer = false;

        foreach ($players as $player) {
            $this->buildPossibleOpponentsMap($player);
        }
    }

    /**
     * Fill property $possibleOpponentsMap
     *
     * @param TournamentPlayer $player
     */
    private function buildPossibleOpponentsMap(TournamentPlayer $player)
    {
        $this->possibleOpponentsMap[$player->getId()]['player'] = $player;

        $queryBuilder = $this->container->get("doctrine")
                             ->getRepository("CoreBundle:TournamentPlayer")
                             ->createQueryBuilder("tp");

        $queryBuilder->andWhere("tp.player != :player")
                     ->setParameter("player", $player);

        $queryBuilder->andWhere("tp.tournament = :tournament")
                     ->setParameter("tournament", $player->getTournament());

        $queryBuilder->addOrderBy("tp.points", "DESC")
            ->addOrderBy("tp.coefficient", "DESC");

        /** @var TournamentPlayer[] $possibleOpponents */
        $possibleOpponents = $queryBuilder->getQuery()->getResult();

        foreach ($possibleOpponents as $opponent) {
            if ($opponent == $player) {
                continue;
            }

            if (
                in_array($opponent->getPlayer()->getId(), $player->getOpponents()) ||
                in_array($player->getPlayer()->getId(), $opponent->getOpponents())
            ) {
                continue;
            }

            if (!$this->ignoreColors &&
                $player->getRequiredColor() != GameColor::RANDOM &&
                $opponent->getRequiredColor() == $player->getRequiredColor()) {
                continue;
            }

            $this->possibleOpponentsMap[$player->getId()]['opponents'][$opponent->getId()] = $opponent;
        }
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function createNewGamesFromPossibleOpponentsMap(Tournament $tournament, int $round)
    {
        $alreadyPlayed = [];

        foreach ($this->possibleOpponentsMap as $playerId => $playerArray) {
            if (isset($alreadyPlayed[$playerId])) {
                continue;
            }

            /** @var TournamentPlayer $firstPlayer */
            $firstPlayer = $playerArray["player"];

            $secondPlayer = null;

            foreach ((array)@$playerArray["opponents"] as $opponentId => $opponent) {
                if (!isset($alreadyPlayed[$opponentId])) {
                    /** @var TournamentPlayer $secondPlayer */
                    $secondPlayer = $playerArray["opponents"][$opponentId];
                    break;
                }
            }

            if (!$secondPlayer) {
                if (!$this->existsMissedPlayer && !$firstPlayer->isMissedRound()) {
                    $firstPlayer->setPoints($firstPlayer->getPoints() + 1)
                                ->setMissedRound(true);

                    $alreadyPlayed[$firstPlayer->getId()] = $firstPlayer->getId();
                    $this->existsMissedPlayer = true;
                    continue;
                } else {
                    $firstPlayer->setPointsForDraw($firstPlayer->getPointsForDraw() + 0.5);
                    
                    if ($firstPlayer->getPointsForDraw() == 8) {
                        $this->ignoreColors = true;
                    }

                    throw new TournamentDrawIncorrectException(
                        $this->getPlayerInfo($firstPlayer)
                    );
                }

            }

            $this->createTournamentGame($tournament, $round, $firstPlayer, $secondPlayer);

            $alreadyPlayed[$firstPlayer->getId()] = $firstPlayer->getId();
            $alreadyPlayed[$secondPlayer->getId()] = $secondPlayer->getId();
        }
    }

    /**
     * @inheritDoc
     */
    public function calculate(Tournament $tournament)
    {
        foreach ($tournament->getPlayers() as $tournamentPlayer) {
            $tournamentPlayer->setCoefficient($tournamentPlayer->isMissedRound() ? $tournament->getRounds() / 2 : 0)
                             ->setPoints($tournamentPlayer->isMissedRound() ? 1 : 0);
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            $this->updatePoints(
                $tournamentGame->getPlayerWhite(),
                (float)$tournamentGame->getGame()->getResultWhite()
            );

            $this->updatePoints(
                $tournamentGame->getPlayerBlack(),
                (float)$tournamentGame->getGame()->getResultBlack()
            );
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            $this->updateCoefficients(
                $tournamentGame->getPlayerWhite(),
                $tournamentGame->getPlayerBlack(),
                $tournamentGame->getGame()->getResultWhite()
            );

            $this->updateCoefficients(
                $tournamentGame->getPlayerBlack(),
                $tournamentGame->getPlayerWhite(),
                $tournamentGame->getGame()->getResultBlack()
            );

            $this->manager->persist($tournamentGame->getPlayerWhite());
            $this->manager->persist($tournamentGame->getPlayerBlack());
        }

        $this->manager->flush();
    }

    /**
     * @param TournamentPlayer $player
     * @param float $result
     */
    private function updatePoints(
        TournamentPlayer $player,
        float $result
    ) {
        $player->setPoints(
            $player->getPoints() + $result
        );
    }

    /**
     * @inheritDoc
     */
    public function updateCoefficients(
        TournamentPlayer $player,
        TournamentPlayer $opponent,
        float $result
    ) {
        $player->setCoefficient(
            $player->getCoefficient() + $opponent->getPoints()
        );
    }

    /**
     * @param TournamentPlayer $player
     * @return int
     */
    private function calculateWhiteAvailabilty(TournamentPlayer $player)
    {
        return 100 * ($player->getCountBlack() - $player->getCountWhite()) +
        0.0001 * mt_rand(-50000, 50000);
    }
}