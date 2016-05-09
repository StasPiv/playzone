<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:14
 */

namespace CoreBundle\Model\Request\Chat;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChatPostMessageRequest
 * @package CoreBundle\Model\Request\Chat
 */
class ChatPostMessageRequest extends ChatRequest implements SecurityRequestInterface
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
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Message is required for this request"
     * )
     */
    private $message;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this|string
     */
    public function setLogin($login)
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
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ChatPostMessageRequest
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
}