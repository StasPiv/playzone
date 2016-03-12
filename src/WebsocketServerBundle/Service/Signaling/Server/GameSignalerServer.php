<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 12:07
 */

namespace WebsocketServerBundle\Service\Signaling\Server;

use Ratchet\ConnectionInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WebsocketServerBundle\Exception\SignalingServerException;
use WebsocketServerBundle\Model\Signaling\ClientMessage\OwnerEnterMessage;
use WebsocketServerBundle\Model\Signaling\ClientMessage\SubscriberEnterMessage;
use WebsocketServerBundle\Model\Signaling\ClientMessage\SubscriberSendDataMessage;
use WebsocketServerBundle\Model\Signaling\ClientMessageAction;
use WebsocketServerBundle\Model\Signaling\ServerMessage\AnswerFromSubscriberServerMessage;
use WebsocketServerBundle\Model\Signaling\ServerMessage\OfferFromOwnerMessage;
use WebsocketServerBundle\Model\Signaling\ServerMessage\SubscriberEnteredMessage;
use WebsocketServerBundle\Model\Signaling\ServerMessageInterface;
use WebsocketServerBundle\Service\Signaling\Server\Member\Owner;
use WebsocketServerBundle\Service\Signaling\Server\Member\Subscriber;
use WebsocketServerBundle\Service\Signaling\SignalerServerInterface;

class GameSignalerServer implements SignalerServerInterface
{
    use ContainerAwareTrait;

    /** @var OutputInterface */
    private $output;

    /** @var Owner[] */
    private $ownersMap = [];

    /** @var array */
    private $subscribersMap = [];

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return GameSignalerServer
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

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
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->getOutput()->writeln($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $clientMessageArray = json_decode($msg, true);

        if (!isset($clientMessageArray['action'])) {
            throw new SignalingServerException("Client message should contain action key");
        }

        switch ($clientMessageArray['action']) {
            case ClientMessageAction::OWNER_ENTER():
                $this->processForOwnerEnterMessage($from,
                    $this->container->get("ws.service.signaling.client_message_factory")
                         ->createOwnerEnterMessage($msg));
                break;
            case ClientMessageAction::SUBSCRIBER_ENTER():
                $this->processForSubscriberEnterMessage($from,
                    $this->container->get("ws.service.signaling.client_message_factory")
                        ->createSubscriberEnterMessage($msg));
                break;
            case ClientMessageAction::SUBSCRIBER_SEND_DATA():
                $this->processForSubscriberSendDataMessage($from,
                    $this->container->get("ws.service.signaling.client_message_factory")
                        ->createSubscriberSendDataMessage($msg));
                break;
            default:
                throw new SignalingServerException("Unknown action in client message");
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->getOutput()->writeln("close");
        foreach ($this->subscribersMap as $roomSubscribers) {
            /** @var Subscriber[] $roomSubscribers */
            foreach ($roomSubscribers as $name => $suscriber) {
                if ($suscriber->getConnection() == $conn) {
                    $this->getOutput()->writeln("close $name");
                    unset($roomSubscribers[$name]);
                }
            }
        }

        foreach ($this->ownersMap as $roomId => $owner) {
            if ($owner->getConnection() == $conn) {
                $this->getOutput()->writeln("close $roomId");
                unset($this->ownersMap[$roomId]);
            }
        }
    }

    private function processForOwnerEnterMessage(ConnectionInterface $from, OwnerEnterMessage $ownerEnterMessage)
    {
        $this->addOwner($from, $ownerEnterMessage);
        $this->sendOfferFromOwnerMessageToSubscribers($ownerEnterMessage);
    }

    private function processForSubscriberEnterMessage(
        ConnectionInterface $from,
        SubscriberEnterMessage $subscriberEnterMessage
    )
    {
        $this->addSubscriber($from, $subscriberEnterMessage);
        $this->sendSubscriberEnteredMessageToOwner($subscriberEnterMessage);
    }

    private function processForSubscriberSendDataMessage(
        ConnectionInterface $from,
        SubscriberSendDataMessage $subscriberSendDataMessage
    )
    {
        $this->sendAnswerFromSubscriberToOwner($subscriberSendDataMessage);
    }

    /**
     * @param ConnectionInterface $from
     * @param OwnerEnterMessage $ownerEnterMessage
     */
    private function addOwner(ConnectionInterface $from, OwnerEnterMessage $ownerEnterMessage)
    {
        $this->ownersMap[$ownerEnterMessage->getRoom()] =
            (new Owner())->setConnection($from)
                ->setCandidate($ownerEnterMessage->getCandidate())
                ->setOfferSdpDescription($ownerEnterMessage->getOfferSdpDescription())
                ->setRoom($ownerEnterMessage->getRoom());
    }

    /**
     * @param ConnectionInterface $from
     * @param SubscriberEnterMessage $subscriberEnterMessage
     */
    private function addSubscriber(ConnectionInterface $from, SubscriberEnterMessage $subscriberEnterMessage)
    {
        $this->subscribersMap[$subscriberEnterMessage->getRoom()][] =
            (new Subscriber())->setConnection($from);
    }

    /**
     * @param OwnerEnterMessage $ownerEnterMessage
     */
    private function sendOfferFromOwnerMessageToSubscribers(OwnerEnterMessage $ownerEnterMessage)
    {
        if (!isset($this->subscribersMap[$ownerEnterMessage->getRoom()])) {
            return;
        }

        foreach ($this->subscribersMap[$ownerEnterMessage->getRoom()] as $subscriber) {
            /** @var Subscriber $subscriber */
            $this->send(
                $subscriber->getConnection(),
                (new OfferFromOwnerMessage())->setRoom($ownerEnterMessage->getRoom())
                    ->setCandidate($ownerEnterMessage->getCandidate())
                    ->setOfferSdpDescription($ownerEnterMessage->getOfferSdpDescription())
            );
        }
    }

    /**
     * @param SubscriberEnterMessage $subscriberEnterMessage
     */
    private function sendSubscriberEnteredMessageToOwner(SubscriberEnterMessage $subscriberEnterMessage)
    {
        if (!isset($this->ownersMap[$subscriberEnterMessage->getRoom()])) {
            return;
        }

        $this->send(
            $this->ownersMap[$subscriberEnterMessage->getRoom()]->getConnection(),
            (new SubscriberEnteredMessage())->setName($subscriberEnterMessage->getName())
                ->setRoom($subscriberEnterMessage->getRoom())
        );
    }

    /**
     * @param SubscriberSendDataMessage $subscriberSendDataMessage
     */
    private function sendAnswerFromSubscriberToOwner(SubscriberSendDataMessage $subscriberSendDataMessage)
    {
        if (!isset($this->ownersMap[$subscriberSendDataMessage->getRoom()])) {
            return;
        }

        $this->send(
            $this->ownersMap[$subscriberSendDataMessage->getRoom()]->getConnection(),
            (new AnswerFromSubscriberServerMessage())->setRoom($subscriberSendDataMessage->getRoom())
                ->setCandidate($subscriberSendDataMessage->getCandidate())
                ->setAnswerSdpDescription($subscriberSendDataMessage->getAnswerSdpDescription())
        );
    }

    private function send(ConnectionInterface $connection, ServerMessageInterface $serverMessage)
    {
        $connection->send(
            $this->container->get("jms_serializer")->serialize($serverMessage, 'json')
        );
    }
}