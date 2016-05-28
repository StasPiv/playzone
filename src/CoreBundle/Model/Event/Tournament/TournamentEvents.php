<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.05.16
 * Time: 21:13
 */

namespace CoreBundle\Model\Event\Tournament;

/**
 * Class TournamentEvents
 * @package CoreBundle\Model\Event\Tournament
 */
class TournamentEvents
{
    const TOURNAMENT_NEW = 'tournament.new';
    const ROUND_FINISHED = 'round_finished';
}