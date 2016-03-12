<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 22:50
 */

namespace WebsocketServerBundle\Model\Signaling;

use MyCLabs\Enum\Enum;

/**
 * Class ClientMessageType
 * @package WebsocketServerBundle\Model\Singlaling
 *
 * @method static ClientMessageAction OWNER_ENTER()
 * @method static ClientMessageAction SUBSCRIBER_SEND_DATA()
 * @method static ClientMessageAction SUBSCRIBER_ENTER()
 */
class ClientMessageAction extends Enum
{
    const OWNER_ENTER = 'owner-enter';
    const SUBSCRIBER_SEND_DATA = 'subscriber-send-data';
    const SUBSCRIBER_ENTER = 'subscriber-enter';
}