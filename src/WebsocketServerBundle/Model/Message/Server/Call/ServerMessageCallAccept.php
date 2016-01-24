<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 22:03
 */

namespace WebsocketServerBundle\Model\Message\Server\Call;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Entity\Game;

class ServerMessageCallAccept
{
    /**
     * @var Game
     *
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Entity\Game")
     */
    private $game;

    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $callId;

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return int
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * @param int $callId
     */
    public function setCallId($callId)
    {
        $this->callId = $callId;
    }
}