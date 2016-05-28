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
    const NEW = 'tournament.new';
    const START = 'tournament.start';
    const ROUND_START = 'tournament.round_start';
    const ROUND_FINISHED = 'tournament.round_finished';
}