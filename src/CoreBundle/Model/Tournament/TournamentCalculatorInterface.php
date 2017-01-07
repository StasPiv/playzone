<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.17
 * Time: 23:38
 */

namespace CoreBundle\Model\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;

/**
 * Interface TournamentCalculatorInterface
 * @package CoreBundle\Model\Tournament
 */
interface TournamentCalculatorInterface
{
    /**
     * @param Tournament $tournament
     * @return mixed
     */
    public function calculate(Tournament $tournament);

    /**
     * @param TournamentPlayer $player
     * @param TournamentPlayer $opponent
     * @param float $result
     * @return mixed
     */
    public function updateCoefficients(
        TournamentPlayer $player,
        TournamentPlayer $opponent,
        float $result
    );
}