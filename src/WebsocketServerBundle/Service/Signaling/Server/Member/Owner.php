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

class Owner implements MemberInterface
{
    /**
     * @var string
     */
    private $room;

    /**
     * @var string
     */
    private $offerSdpDescription;

    /**
     * @var string
     */
    private $candidate;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     * @return Owner
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return string
     */
    public function getOfferSdpDescription()
    {
        return $this->offerSdpDescription;
    }

    /**
     * @param string $offerSdpDescription
     * @return Owner
     */
    public function setOfferSdpDescription($offerSdpDescription)
    {
        $this->offerSdpDescription = $offerSdpDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param string $candidate
     * @return Owner
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;

        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     * @return Owner
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}