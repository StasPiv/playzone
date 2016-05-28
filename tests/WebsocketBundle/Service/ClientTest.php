<?php

namespace WebsocketBundle\Tests\Service;

use CoreBundle\Entity\Event;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\Params\TournamentRoundrobinParams;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.05.16
 * Time: 23:01
 */
class ClientTest extends KernelAwareTest
{

    public function testEchoClient()
    {
        $client = new \WebSocket\Client("ws://echo.websocket.org/");
        $client->send("Hello WebSocket.org!");

        echo $client->receive(); // Will output 'Hello WebSocket.org!'
    }
}