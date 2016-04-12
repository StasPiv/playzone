<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.04.16
 * Time: 22:15
 */
namespace CoreBundle\Model\Tournament;

use CoreBundle\Entity\Tournament;


/**
 * Class SwissService
 * @package CoreBundle\Service
 */
interface TournamentDrawInterface
{
    /**
     * @param Tournament $tournament
     * @param int $round
     * @return
     */
    public function makeDraw(Tournament $tournament, int $round);
}