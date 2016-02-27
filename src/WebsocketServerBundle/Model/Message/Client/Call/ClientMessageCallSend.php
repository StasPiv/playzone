<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 20:26
 */
namespace WebsocketServerBundle\Model\Message\Client\Call;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ClientMessageCallSend
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $login;

    /**
     * @var array
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    private $callIds = [];

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

    /**
     * @return array
     */
    public function getCallIds()
    {
        return $this->callIds;
    }

    /**
     * @param array $callIds
     */
    public function setCallIds($callIds)
    {
        $this->callIds = $callIds;
    }
}