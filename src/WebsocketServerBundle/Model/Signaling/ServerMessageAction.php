<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 12:50
 */

namespace WebsocketServerBundle\Model\Signaling;

use MyCLabs\Enum\Enum;

/**
 * Class ServerMessageAction
 * @package WebsocketServerBundle\Model\Signaling
 *
 * @method static ServerMessageAction OFFER_FROM_OWNER()
 * @method static ServerMessageAction ANSWER_FROM_SUBSCRIBER()
 * @method static ServerMessageAction SUBSCRIBER_ENTERED()
 */
class ServerMessageAction extends Enum
{
    const OFFER_FROM_OWNER = 'offer-from-owner';
    const ANSWER_FROM_SUBSCRIBER = 'answer-from-subscriber';
    const SUBSCRIBER_ENTERED = 'subscriber-entered';
}