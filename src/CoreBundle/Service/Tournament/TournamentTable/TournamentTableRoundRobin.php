<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 10:54
 */

namespace CoreBundle\Service\Tournament\TournamentTable;


use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Model\Tournament\TournamentGame\TournamentGameRoundRobin;
use CoreBundle\Service\Tournament\TournamentTableInterface;

/**
 * Class TournamentTableRoundRobin
 * @package CoreBundle\Service\Tournament\TournamentTable
 */
class TournamentTableRoundRobin implements TournamentTableInterface
{
    /**
     * @param Tournament $tournament
     * @return void
     */
    public function mixTournamentTable(Tournament $tournament)
    {
        $this->mixAllGames($tournament);
        $this->mixGamesForRoundRobin($tournament);
    }

    /**
     * @param Tournament $tournament
     */
    private function mixAllGames(Tournament $tournament)
    {
        $games = [];
        foreach ($tournament->getGames() as $tournamentGame) {
            /** @var TournamentGame $tournamentGame */
            $games[] = $tournamentGame->getGame();
        }
        $tournament->setAllGames($games);
    }

    /**
     * @param Tournament $tournament
     */
    private function mixGamesForRoundRobin(Tournament $tournament)
    {
        $gamesMap = [];

        foreach ($tournament->getPlayers() as $firstPlayer) {
            foreach ($tournament->getPlayers() as $secondPlayer) {
                if ($firstPlayer == $secondPlayer) {
                    continue;
                }
                $gamesMap[$firstPlayer->getId()][$secondPlayer->getId()] = '';
                $gamesMap[$secondPlayer->getId()][$firstPlayer->getId()] = '';
            }
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            $playerWhite = $tournamentGame->getPlayerWhite();
            $playerBlack = $tournamentGame->getPlayerBlack();

            $gamesMap[$playerWhite->getId()][$playerBlack->getId()] =
                new TournamentGameRoundRobin(
                    $tournamentGame->getGame()->getId(),
                    $tournamentGame->getGame()->getResultWhite()
                );

            $gamesMap[$playerBlack->getId()][$playerWhite->getId()] =
                new TournamentGameRoundRobin(
                    $tournamentGame->getGame()->getId(),
                    $tournamentGame->getGame()->getResultBlack()
                );
        }

        $tournament->setResultsForRoundRobin($gamesMap);
    }

}