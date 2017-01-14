<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.08.16
 * Time: 13:19
 */

namespace WebsocketClientBundle\Service;

use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Game\GameStatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebsocketClientBundle\Service\Client\PlayzoneClientSender;

/**
 * Class ShutdownServerService
 * @package WebsocketClientBundle\Service
 */
class ShutdownServerService implements EventSubscriberInterface
{
    /** @var PlayzoneClientSender */
    private $clientSender;

    /** @var string */
    const LOGIN = 'Robot';

    /** @var string */
    const TOKEN = '407f20f52463392c43bf6a58b783c4f2';

    private $defaultWsServerUrl = 'ws://ws.pozitiffchess.net:8081/';

    /**
     * ShutdownServerService constructor.
     * @param PlayzoneClientSender $clientSender
     */
    public function __construct(PlayzoneClientSender $clientSender)
    {
        $this->clientSender = $clientSender;
    }

    /**
     * @param string $wsServerUrl
     * @param string $login
     * @param string $token
     */
    public function shutdownServer(
        string $wsServerUrl,
        string $login = self::LOGIN,
        string $token = self::TOKEN
    )
    {
        $wsClient = new PlayzoneClient(
            $wsServerUrl,
            [
                'timeout' => -1
            ]
        );

        $this->clientSender
            ->sendShutdown(
                $wsClient,
                $login,
                $token
            );
    }

    /**
     * @param string $wsServerUrl
     * @param int $gameId
     */
    public function sendGameFinish(
        string $wsServerUrl,
        int $gameId
    )
    {
        $wsClient = new PlayzoneClient(
            $wsServerUrl,
            [
                'timeout' => -1
            ]
        );

        $this->clientSender->sendGameFinish($wsClient, $gameId);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            GameEvents::CHANGE_STATUS_BEFORE => [
                ['onGameChangeStatusBefore', 10],
            ],
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function onGameChangeStatusBefore(GameEvent $event)
    {
        if (!in_array($event->getGame()->getStatus(), [GameStatus::END, GameStatus::ABORTED])) {
            return;
        }

        $this->sendGameFinish($this->defaultWsServerUrl, $event->getGame()->getId());
    }
}