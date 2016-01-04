<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.01.16
 * Time: 22:13
 */

namespace WebsocketServerBundle\Service;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatServer implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $new)
    {
        $this->clients->attach($new);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $from->send("Message sent");
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $gone)
    {
        $this->clients->detach($gone);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}