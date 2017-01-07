<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 10:55
 */

namespace CoreBundle\Service\Tournament\TournamentTable;

use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Tournament\TournamentGame\TournamentGameSwitz;
use CoreBundle\Service\Tournament\TournamentTableInterface;

/**
 * Class TournamentTableSwitz
 * @package CoreBundle\Service\Tournament\TournamentTable
 */
class TournamentTableSwitz implements TournamentTableInterface
{
    /**
     * @param Tournament $tournament
     * @return void
     */
    public function mixTournamentTable(Tournament $tournament)
    {
        $gamesMap = [];

        foreach ($tournament->getPlayers() as $player) {
            $gamesMap[$player->getId()]["player"] = $player;
            for ($round = 1; $round <= $tournament->getCurrentRound(); $round++) {
                $gamesMap[$player->getId()]["rounds"][$round] = [];
            }
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            if ($tournamentGame->getGame()->getStatus() != GameStatus::END) {
                continue;
            }
            
            $playerWhite = $tournamentGame->getPlayerWhite();
            $playerBlack = $tournamentGame->getPlayerBlack();

            $gamesMap[$playerWhite->getId()]["rounds"][$tournamentGame->getRound()] =
                new TournamentGameSwitz(
                    $tournamentGame->getGame()->getId(),
                    GameColor::WHITE,
                    $tournamentGame->getGame()->getResultWhite(),
                    $playerBlack->getPlayer()
                );

            $gamesMap[$playerBlack->getId()]["rounds"][$tournamentGame->getRound()] =
                new TournamentGameSwitz(
                    $tournamentGame->getGame()->getId(),
                    GameColor::BLACK,
                    $tournamentGame->getGame()->getResultBlack(),
                    $playerWhite->getPlayer()
                );
        }

        $tournament->setResultsForSwiss($gamesMap);
        $this->mixAllGames($tournament);
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

}