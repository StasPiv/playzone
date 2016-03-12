<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:29
 */

namespace WebsocketServerBundle\Model\Signaling;

interface ServerMessageInterface
{
    /**
     * @return ServerMessageAction
     */
    public function getAction();

    /**
     * @return string
     */
    public function getRoom();
}