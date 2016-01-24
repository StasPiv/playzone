<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 20:09
 */

namespace WebsocketServerBundle\Handler;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WebsocketServerBundle\Exception\Handler\ClientMessageHandlerException;
use WebsocketServerBundle\Model\Message\Client\Call\ClientMessageCallAccept;
use WebsocketServerBundle\Model\Message\Client\Call\ClientMessageCallDecline;
use WebsocketServerBundle\Model\Message\Client\Call\ClientMessageCallDelete;
use WebsocketServerBundle\Model\Message\Client\Call\ClientMessageCallSend;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketServerBundle\Model\Message\Server\Call\ServerMessageCallAccept;

class ClientMessageHandler
{
    use ContainerAwareTrait;

    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     */
    public function prepareMessageForUsers(PlayzoneMessage $playzoneMessage)
    {
        switch ($playzoneMessage->getMethod()) {
            case PlayzoneClientMessageMethod::CALL_SEND:
                $this->prepareCallSend($playzoneMessage);
                break;
            case PlayzoneClientMessageMethod::CALL_DECLINE:
                $this->prepareCallDecline($playzoneMessage);
                break;
            case PlayzoneClientMessageMethod::CALL_ACCEPT:
                $this->prepareCallAccept($playzoneMessage);
                break;
            case PlayzoneClientMessageMethod::CALL_DELETE:
                $this->prepareCallDelete($playzoneMessage);
                break;
        }
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     */
    private function prepareCallSend(PlayzoneMessage $playzoneMessage)
    {
        $data = $this->getClientMessageObject($playzoneMessage, 'Call\ClientMessageCallSend');

        if (!$data instanceof ClientMessageCallSend) {
            throw new ClientMessageHandlerException("Unknown type of sent data");
        }

        $playzoneMessage->setData(
            $this->container->get("core.handler.game.call")->getUserCallsByLogin(
                $data->getLogin(), "toUser", $data->getCallIds()
            )
        );
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     */
    private function prepareCallDecline(PlayzoneMessage $playzoneMessage)
    {
        $data = $this->getClientMessageObject($playzoneMessage, 'Call\ClientMessageCallDecline');

        if (!$data instanceof ClientMessageCallDecline) {
            throw new ClientMessageHandlerException("Unknown type of sent data");
        }
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     */
    private function prepareCallDelete(PlayzoneMessage $playzoneMessage)
    {
        $data = $this->getClientMessageObject($playzoneMessage, 'Call\ClientMessageCallDelete');

        if (!$data instanceof ClientMessageCallDelete) {
            throw new ClientMessageHandlerException("Unknown type of sent data");
        }
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     */
    private function prepareCallAccept(PlayzoneMessage $playzoneMessage)
    {
        $data = $this->getClientMessageObject($playzoneMessage, 'Call\ClientMessageCallAccept');

        if (!$data instanceof ClientMessageCallAccept) {
            throw new ClientMessageHandlerException("Unknown type of sent data");
        }

        $serverCallAccept = new ServerMessageCallAccept();
        $serverCallAccept->setGame(
            $this->container->get("core.handler.game")->getUserGameByLogin($data->getLogin(), $data->getGameId())
        );
        $serverCallAccept->setCallId($data->getCallId());

        $playzoneMessage->setData(
            json_decode(
                $this->container->get("jms_serializer")->serialize($serverCallAccept, 'json'),
                true
            )
        );
    }

    /**
     * @param PlayzoneMessage $playzoneMessage
     * @param $clientMessageType
     * @return object
     */
    private function getClientMessageObject(PlayzoneMessage $playzoneMessage, $clientMessageType)
    {
        $data = $this->container->get("jms_serializer")->deserialize(
            json_encode($playzoneMessage->getData()),
            'WebsocketServerBundle\Model\Message\Client\\' . $clientMessageType,
            'json'
        );

        return $data;
    }
}