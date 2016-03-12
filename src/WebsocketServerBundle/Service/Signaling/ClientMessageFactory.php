<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:08
 */

namespace WebsocketServerBundle\Service\Signaling;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WebsocketServerBundle\Model\Signaling\ClientMessageInterface;
use WebsocketServerBundle\Model\Signaling\ClientMessage\OwnerEnterMessage;
use WebsocketServerBundle\Model\Signaling\ClientMessage\SubscriberEnterMessage;
use WebsocketServerBundle\Model\Signaling\ClientMessage\SubscriberSendDataMessage;

class ClientMessageFactory
{
    use ContainerAwareTrait;

    /**
     * @param string $clientMessage
     * @return OwnerEnterMessage
     */
    public function createOwnerEnterMessage($clientMessage)
    {
        return $this->getClientMessageObject(new OwnerEnterMessage(), $clientMessage);
    }

    /**
     * @param string $clientMessage
     * @return SubscriberEnterMessage
     */
    public function createSubscriberEnterMessage($clientMessage)
    {
        return $this->getClientMessageObject(new SubscriberEnterMessage(), $clientMessage);
    }

    /**
     * @param string $clientMessage
     * @return SubscriberSendDataMessage
     */
    public function createSubscriberSendDataMessage($clientMessage)
    {
        return $this->getClientMessageObject(new SubscriberSendDataMessage(), $clientMessage);
    }

    /**
     * @param ClientMessageInterface $clientMessageObject
     * @param string $clientMessage
     * @return ClientMessageInterface
     */
    private function getClientMessageObject(ClientMessageInterface $clientMessageObject, $clientMessage)
    {
        return $this->container->get("jms_serializer")->deserialize($clientMessage, get_class($clientMessageObject),
            'json');
    }
}