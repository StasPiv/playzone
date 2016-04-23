<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.04.16
 * Time: 22:11
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Tournament\TournamentDrawIncorrectException;
use CoreBundle\Exception\Handler\Tournament\TournamentPlayersAlreadyPlayedException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Tournament\TournamentDrawInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SwissService
 * @package CoreBundle\Service
 */
class SwissService implements TournamentDrawInterface
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
        try {
            $this->processDraw($tournament, $round);
            $this->manager->flush();
        } catch (TournamentDrawIncorrectException $e) {
            $this->container->get("logger")->debug("DRAW no opponents: " . $e->getMessage());
            $this->makeDraw($tournament, $round);
        }
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
                $this->getTournamentHandler()->getAllTournamentPlayers($tournament)
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

        foreach ($this->possibleTournamentGames as $possibleTournamentGame) {
            $this->manager->detach($possibleTournamentGame);
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
                if ($a->getPoints() === $b->getPoints()) {
                    if ($b->getRequiredColor() == $a->getRequiredColor()) {
                        return $b->getPlayer()->getLogin() <=> $a->getPlayer()->getLogin();
                    }

                    return $b->getRequiredColor() <=> $a->getRequiredColor();
                }

                return $b->getPoints() + $b->getPointsForDraw() <=> $a->getPoints() + $a->getPointsForDraw();
            }
        );

        return $tournamentPlayers;
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

        $queryBuilder->addOrderBy("tp.points", "DESC");

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

            if ($player->getRequiredColor() != GameColor::RANDOM &&
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

            if (empty($playerArray["opponents"])) {
                continue;
            }

            $secondPlayer = null;

            foreach ($playerArray["opponents"] as $opponentId => $opponent) {
                if (!isset($alreadyPlayed[$opponentId])) {
                    /** @var TournamentPlayer $secondPlayer */
                    $secondPlayer = $playerArray["opponents"][$opponentId];
                    break;
                }
            }

            if (!$secondPlayer) {
                $firstPlayer->setPointsForDraw($firstPlayer->getPointsForDraw() + 0.5);

                throw new TournamentDrawIncorrectException;
            }

            $this->createTournamentGame($tournament, $round, $firstPlayer, $secondPlayer);

            $alreadyPlayed[$firstPlayer->getId()] = $firstPlayer->getId();
            $alreadyPlayed[$secondPlayer->getId()] = $secondPlayer->getId();
        }
    }
}