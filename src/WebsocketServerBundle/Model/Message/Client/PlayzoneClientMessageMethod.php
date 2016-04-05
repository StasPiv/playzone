<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 20:06
 */

namespace WebsocketServerBundle\Model\Message\Client;

use MyCLabs\Enum\Enum;

/**
 * Class PlayzoneClientMessageMethod
 * @package WebsocketServerBundle\Model\Message\Client
 *
 * @method static PlayzoneClientMessageMethod CALL_ACCEPT()
 * @method static PlayzoneClientMessageMethod CALL_SEND()
 * @method static PlayzoneClientMessageMethod CALL_DELETE()
 * @method static PlayzoneClientMessageMethod CALL_DECLINE()
 * @method static PlayzoneClientMessageMethod SEND_PGN_TO_OBSERVERS()
 */
class PlayzoneClientMessageMethod extends Enum
{
    const CALL_ACCEPT = 'call_accept';
    const CALL_SEND = 'call_send';
    const CALL_DELETE = 'call_delete';
    const CALL_DECLINE = 'call_decline';
    const SEND_PGN_TO_OBSERVERS = 'send_pgn_to_observers';
    const USER_IN = 'user_in';
    const USER_GONE = 'user_gone';
    const WELCOME_MESSAGE = 'welcome';
}