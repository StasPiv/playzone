<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 08.10.16
 * Time: 1:57
 */

namespace WebsocketClientBundle\Service;


use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use WebsocketClientBundle\Service\Client\PlayzoneClientSender;

/**
 * Class PingServerService
 * @package WebsocketClientBundle\Service
 */
class PingServerService implements EventCommandInterface
{
    /** @var PlayzoneClientSender */
    private $clientSender;

    /**
     * PingServerService constructor.
     * @param PlayzoneClientSender $clientSender
     */
    public function __construct(PlayzoneClientSender $clientSender)
    {
        $this->clientSender = $clientSender;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $wsClient = new PlayzoneClient(
            'ws://ws.pozitiffchess.net:8081/',
            [
                'timeout' => -1
            ]
        );

        $this->clientSender
             ->sendIntroductionFromRobot($wsClient);
    }

    /**
     * @inheritDoc
     */
    public function setEventModel(EventInterface $eventModel)
    {
        // TODO: Implement setEventModel() method.
    }

}