<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:06
 */

namespace WebsocketServerBundle\Model\Signaling;

interface ClientMessageInterface
{
    /**
     * @return ClientMessageAction
     */
    public function getAction();

    /**
     * @return string
     */
    public function getRoom();
}