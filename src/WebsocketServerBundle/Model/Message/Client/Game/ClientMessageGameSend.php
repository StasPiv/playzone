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
 * Class ClientMessageGameSend
 * @package WebsocketServerBundle\Model\Message\Client\Game
 */
class ClientMessageGameSend
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
    private $encodedPgn;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $encodedFen;

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
    public function getEncodedPgn()
    {
        return $this->encodedPgn;
    }

    /**
     * @param string $encodedPgn
     * @return ClientMessageGameSend
     */
    public function setEncodedPgn($encodedPgn)
    {
        $this->encodedPgn = $encodedPgn;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodedFen() : string 
    {
        return $this->encodedFen;
    }

    /**
     * @param string $encodedFen
     * @return ClientMessageGameSend
     */
    public function setEncodedFen(string $encodedFen)
    {
        $this->encodedFen = $encodedFen;

        return $this;
    }
}