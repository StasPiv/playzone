<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.03.16
 * Time: 16:47
 */

namespace WebsocketServerBundle\Model\Message\Client\Game;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClientMessageMessageSend
 * @package WebsocketServerBundle\Model\Message\Client\Game
 */
class ClientMessageMessageSend
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $gameId;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $message;

    /**
     * @return string
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param string $gameId
     * @return ClientMessageGameSend
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ClientMessageGameSend
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}