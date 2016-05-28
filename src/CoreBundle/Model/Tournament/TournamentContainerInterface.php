<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.05.16
 * Time: 0:05
 */

namespace CoreBundle\Model\Tournament;

/**
 * Interface TournamentContainerInterface
 * @package CoreBundle\Model\Tournament
 */
interface TournamentContainerInterface
{
    /**
     * @return int
     */
    function getTournamentId() : int;
}