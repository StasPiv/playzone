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

class SignalerServer implements MessageComponentInterface
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
                if ($suscriber == $conn) {
                    unset($roomSubscribers[$name]);
                }
            }
        }

        foreach ($this->owners as $roomId => $owner) {
            if ($owner == $conn) {
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
        $name = $jsonMessage['name'];
        $this->getOutput()->writeln($jsonMessage['action']);
        switch ($jsonMessage['action']) {
            case 'join':
                $this->subscribers[$room][$name] = $from;
                if (isset($this->owners[$room])) {
                    $from->send(json_encode([
                        'action' => 'you-are-joined',
                        'room' => $room
                    ]));
                    $this->owners[$room]->send(json_encode([
                        'action' => 'subscriber-joined',
                        'room' => $room
                    ]));
                } else {
                    $from->send(json_encode([
                        'action' => 'not-created-yet',
                        'room' => $room
                    ]));
                }
                break;
            case 'create':
                $this->owners[$room] = $from;
                $from->send(json_encode([
                    'action' => 'created-by-you',
                    'room' => $room
                ]));
                /** @var ConnectionInterface[] $roomSubscribers */
                $roomSubscribers = $this->subscribers[$room];
                foreach ($roomSubscribers as $subscriber) {
                    $subscriber->send(json_encode([
                        'action' => 'offer-from-owner',
                        'room' => $room,
                        'offerSDP' => $jsonMessage['offer']['sdp']
                    ]));
                }
                break;
            case 'join-and-prepare-answer':
                if (isset($this->owners[$room])) {
                    $this->owners[$room]->send(json_encode([
                        'action' => 'answer-from-subscriber',
                        'room' => $room,
                        'answerSDP' => $jsonMessage['answer']['sdp']
                    ]));
                }
                break;
            case 'ice-candidate-from-subscriber':
                if (isset($this->owners[$room])) {
                    $this->owners[$room]->send(json_encode([
                        'action' => 'subscriber-sent-ice-candidate',
                        'room' => $room,
                        'candidate' => $jsonMessage['candidate']
                    ]));
                }
                break;
            case 'ice-candidate-from-owner':
                /** @var ConnectionInterface[] $roomSubscribers */
                $roomSubscribers = $this->subscribers[$room];
                foreach ($roomSubscribers as $subscriber) {
                    $subscriber->send(json_encode([
                        'action' => 'owner-sent-ice-candidate',
                        'room' => $room,
                        'candidate' => $jsonMessage['candidate']
                    ]));
                }
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
     * @return SignalerServer
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

}