<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 20:25
 */

namespace CoreBundle\Model\Tournament;

use CoreBundle\Entity\Tournament;

/**
 * Interface TournamentEventInterface
 * @package CoreBundle\Model\Event
 */
interface TournamentInitializatorInterface
{
    /**
     * @return Tournament
     */
    function initTournament() : Tournament;

    /**
     * @return string
     */
    public function getTimeBegin() : string;
}