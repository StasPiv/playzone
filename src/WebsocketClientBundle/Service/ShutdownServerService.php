<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.08.16
 * Time: 13:19
 */

namespace WebsocketClientBundle\Service;

use WebsocketClientBundle\Service\Client\PlayzoneClientSender;

/**
 * Class ShutdownServerService
 * @package WebsocketClientBundle\Service
 */
class ShutdownServerService
{
    /** @var PlayzoneClientSender */
    private $clientSender;

    /** @var string */
    const LOGIN = 'Robot';

    /** @var string */
    const TOKEN = '407f20f52463392c43bf6a58b783c4f2';

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
}