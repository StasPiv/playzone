<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 12:36
 */

namespace WebsocketServerBundle\Service\Signaling\Server;

use Ratchet\ConnectionInterface;

interface MemberInterface
{
    /**
     * @param ConnectionInterface $connection
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * @return ConnectionInterface
     */
    public function getConnection();
}