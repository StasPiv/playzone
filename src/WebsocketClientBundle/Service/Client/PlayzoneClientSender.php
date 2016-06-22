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
        $this->send(
            $client,
            (new PlayzoneMessage())->setMethod("introduction")
                ->setScope("introduction")
                ->setData([
                    "login" => "Robot",
                    "token" => "407f20f52463392c43bf6a58b783c4f2"
                ])
        );
    }
}