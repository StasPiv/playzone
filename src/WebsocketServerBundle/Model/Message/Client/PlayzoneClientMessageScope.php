<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.01.16
 * Time: 23:18
 */

namespace WebsocketServerBundle\Model\Message\Client;

use MyCLabs\Enum\Enum;

/**
 * Class PlayzoneClientMessageScope
 * @package WebsocketServerBundle\Model\Message\Client
 *
 * @method static PlayzoneClientMessageScope INTRODUCTION()
 * @method static PlayzoneClientMessageScope SEND_TO_USERS()
 * @method static PlayzoneClientMessageScope SEND_TO_GAME_OBSERVERS()
 * @method static PlayzoneClientMessageScope SUBSCRIBE_TO_GAME()
 */
class PlayzoneClientMessageScope extends Enum
{
    const INTRODUCTION = 'introduction';
    const SEND_TO_USERS = 'send_to_users';
    const SEND_TO_GAME_OBSERVERS = 'send_to_game_observers';
    const SUBSCRIBE_TO_GAME = 'subscribe_to_game';
}