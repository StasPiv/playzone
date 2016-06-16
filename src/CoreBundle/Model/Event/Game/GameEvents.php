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
    const CHANGE_STATUS = 'game.change_status';
    const PUBLISH_FEN = 'game.publish.fen';
    const PUBLISH_PGN = 'game.publish.pgn';
}