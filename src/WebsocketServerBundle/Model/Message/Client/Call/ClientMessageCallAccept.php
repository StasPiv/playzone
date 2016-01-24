<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 21:46
 */

namespace WebsocketServerBundle\Model\Message\Client\Call;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ClientMessageCallAccept
{
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $gameId;

    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $callId;

    /**
     * @var \testFormalParameterUsedInDoubleQuoteStringGetsNotReported
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $login;

    /**
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param int $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
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

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }
}