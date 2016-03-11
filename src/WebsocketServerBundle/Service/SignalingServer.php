<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 08.03.16
 * Time: 0:07
 */

namespace WebsocketServerBundle\Service;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SignalingServer implements MessageComponentInterface
{
    /** @var OutputInterface */
    private $output;

    /** @var array */
    private $subscribers = [];

    /** @var ConnectionInterface[] */
    private $owners = [];

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->getOutput()->writeln("open");
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->getOutput()->writeln("close");
        foreach ($this->subscribers as $roomSubscribers) {
            /** @var ConnectionInterface[] $roomSubscribers */
            foreach ($roomSubscribers as $name => $suscriber) {
                if ($suscriber['connection'] == $conn) {
                    $this->getOutput()->writeln("close $name");
                    unset($roomSubscribers[$name]);
                }
            }
        }

        foreach ($this->owners as $roomId => $owner) {
            if ($owner['connection'] == $conn) {
                $this->getOutput()->writeln("close $roomId");
                unset($this->owners[$roomId]);
            }
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        //$conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $jsonMessage = json_decode($msg, true);
        var_dump($jsonMessage);

        $room = $jsonMessage['room'];
        switch ($jsonMessage['action']) {
            case 'owner-enter':
                $this->addOwnerWithOffer($from, $jsonMessage);

                if (!isset($this->subscribers[$room])) {
                    break;
                }

                foreach ($this->subscribers[$room] as $subscriber) {
                    /** @var ConnectionInterface $subscriberConnection */
                    $subscriberConnection = $subscriber['connection'];
                    $this->sendOwnerOffer($subscriberConnection, $room);
                }
                break;
            case 'subscriber-send-data':
                var_dump('owners count: ' . count($this->owners));
                if (isset($this->owners[$room])) {
                    /** @var ConnectionInterface $ownerConnection */
                    $ownerConnection = $this->owners[$room]['connection'];
                    $this->sendSubscriberAnswer($ownerConnection, $jsonMessage);
                }
                break;
            case 'subscriber-enter':
                if (isset($this->owners[$room])) {
                    //$this->sendOwnerOffer($from, $room);
                    /** @var ConnectionInterface $ownerConnection */
                    $ownerConnection = $this->owners[$room]['connection'];
                    $ownerConnection->send(json_encode([
                        'action' => 'subscriber-entered',
                        'room' => $jsonMessage['room'],
                        'name' => $jsonMessage['name']
                    ]));;
                }
                $this->subscribers[$room][$jsonMessage['name']] = [
                    'connection' => $from
                ];
                break;
        }
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return SignalingServer
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param ConnectionInterface $subscriberConnection
     * @param $room
     */
    private function sendOwnerOffer(ConnectionInterface $subscriberConnection, $room)
    {
        $subscriberConnection->send(json_encode([
            'action' => 'offer-from-owner',
            'room' => $room,
            'candidate' => $this->owners[$room]['candidate'],
            'offerSdpDescription' => $this->owners[$room]['offerSdpDescription']
        ]));
    }

    /**
     * @param ConnectionInterface $ownerConnection
     * @param $jsonMessage
     */
    private function sendSubscriberAnswer(ConnectionInterface $ownerConnection, $jsonMessage)
    {
        $ownerConnection->send(json_encode([
            'action' => 'answer-from-subscriber',
            'candidate' => $jsonMessage['candidate'],
            'answerSdpDescription' => $jsonMessage['answerSdpDescription']
        ]));
    }

    /**
     * @param ConnectionInterface $from
     * @param $jsonMessage
     */
    private function addOwnerWithOffer(ConnectionInterface $from, $jsonMessage)
    {
        $this->owners[$jsonMessage['room']] = [
            'offerSdpDescription' => $jsonMessage['offerSdpDescription'],
            'candidate' => $jsonMessage['candidate'],
            'connection' => $from
        ];
    }

}