<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 12:33
 */

namespace WebsocketServerBundle\Service\Signaling\Server\Member;

use Ratchet\ConnectionInterface;
use WebsocketServerBundle\Service\Signaling\Server\MemberInterface;

class Subscriber implements MemberInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     * @return Subscriber
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}