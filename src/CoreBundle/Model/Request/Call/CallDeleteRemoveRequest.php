<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 18:55
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CallDeleteRemoveRequest extends CallRequest implements SecurityRequestInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Login is required for this request"
     * )
     */
    private $login;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Token is required for this request"
     * )
     */
    private $token;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Call id is required for this request"
     * )
     */
    private $callId;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return CallDeleteRemoveRequest
     */
    public function setLogin($login) : CallDeleteRemoveRequest
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * @param string $callId
     */
    public function setCallId($callId)
    {
        $this->callId = $callId;
    }
}