<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 13:08
 */

namespace CoreBundle\Model\Event\Game;

/**
 * Class GameEvents
 * @package CoreBundle\Model\Event\Tournament
 */
class GameEvents
{
    const CHANGE_STATUS_AFTER = 'game.change_status_after';
    const PUBLISH_FEN = 'game.publish.fen';
    const PUBLISH_PGN = 'game.publish.pgn';
    const CHANGE_STATUS_BEFORE = 'game.change_status_before';
}