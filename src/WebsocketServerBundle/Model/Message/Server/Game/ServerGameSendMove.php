<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.04.16
 * Time: 20:29
 */

namespace WebsocketServerBundle\Model\Message\Server\Game;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ServerGameSendMove
 * @package WebsocketServerBundle\Model\Message\Server\Game
 */
class ServerGameSendMove
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $move;

    /**
     * @return string
     */
    public function getMove()
    {
        return $this->move;
    }

    /**
     * @param string $move
     * @return ServerGameSendMove
     */
    public function setMove($move)
    {
        $this->move = $move;

        return $this;
    }
}