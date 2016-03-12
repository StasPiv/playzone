<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 12:06
 */

namespace WebsocketServerBundle\Service\Signaling;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use WebsocketServerBundle\Model\Signaling\ClientMessageInterface;

interface SignalerServerInterface extends MessageComponentInterface
{

}