<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 16:07
 */

namespace WebsocketServerBundle\Model\Message\Client\Game;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ClientMessageGameSubscribe
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $gameId;

    /**
     * @return string
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param string $gameId
     * @return ClientMessageGameSubscribe
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }
}