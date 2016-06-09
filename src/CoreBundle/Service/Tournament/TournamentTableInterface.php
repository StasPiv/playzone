<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 10:50
 */

namespace CoreBundle\Service\Tournament;

use CoreBundle\Entity\Tournament;

/**
 * Interface TournamentTableInterface
 * @package CoreBundle\Service\Tournament
 */
interface TournamentTableInterface
{
    /**
     * @param Tournament $tournament
     * @return void
     */   
    public function mixTournamentTable(Tournament $tournament);
}