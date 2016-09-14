<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.05.16
 * Time: 22:41
 */

namespace WebsocketClientBundle\Service\Client;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketClientBundle\Service\PlayzoneClient;

/**
 * Class PlayzoneClientSender
 * @package WebsocketServerBundle\Service\Client
 */
class PlayzoneClientSender
{
    use ContainerAwareTrait;

    /**
     * @param PlayzoneClient $client
     * @param PlayzoneMessage $playzoneMessage
     * @return mixed|string
     */
    public function send(PlayzoneClient $client, PlayzoneMessage $playzoneMessage)
    {
        $client->send($this->container->get("serializer")->serialize($playzoneMessage, "json"));
    }

    /**
     * @param PlayzoneClient $client
     */
    public function sendIntroductionFromRobot(PlayzoneClient $client)
    {
        $this->sendIntroduction($client, "Robot", "407f20f52463392c43bf6a58b783c4f2");
    }

    /**
     * @param PlayzoneClient $client
     * @param string $login
     * @param string $token
     */
    public function sendIntroduction(PlayzoneClient $client, string $login, string $token)
    {
        $this->send(
            $client,
            (new PlayzoneMessage())->setMethod("introduction")
                ->setScope("introduction")
                ->setData([
                    "login" => $login,
                    "token" => $token
                ])
        );
    }

    /**
     * @param PlayzoneClient $client
     * @param string $login
     * @param string $token
     */
    public function sendShutdown(PlayzoneClient $client, string $login, string $token)
    {
        $this->send(
            $client,
            (new PlayzoneMessage())->setMethod("stop_server")
                ->setScope("stop_server")
                ->setData([
                    "login" => $login,
                    "token" => $token
                ])
        );
    }

    /**
     * @param PlayzoneClient $client
     * @param int $gameId
     */
    public function sendGameFinish(PlayzoneClient $client, int $gameId)
    {
        $this->send(
            $client,
            (new PlayzoneMessage())->setMethod("game_finish")
                ->setScope("send_to_users")
                ->setData([
                    "game_id" => $gameId
                ])
        );
        $client->close();
    }

    /**
     * @param PlayzoneMessage $message
     */
    public function sendMessageToWebsocketServer(PlayzoneMessage $message)
    {
        $client = $this->container->get("ws.playzone.client");

        $this->sendIntroductionFromRobot($client);

        $this->send($client, $message);
    }
}