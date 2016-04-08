<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 0:42
 */

namespace WebsocketServerBundle\Model\Message\Server;

use JMS\Serializer\Annotation as JMS;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;

/**
 * Class WelcomeMessage
 * @package WebsocketServerBundle\Model\Message\Server
 */
class WelcomeMessage extends PlayzoneMessage
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @return string
     */
    protected $method = PlayzoneClientMessageMethod::WELCOME_MESSAGE;

    /**
     * @var string
     */
    private $login;

    /**
     * @var array
     */
    private $otherLogins = [];

    /**
     * @var array
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    protected $data;

    /**
     * @param $login
     * @param array $otherLogins
     */
    public function __construct($login, $otherLogins = [])
    {
        $this->setLogin($login)
             ->setData([
                'other_logins' => $otherLogins
             ]);
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return WelcomeMessage
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
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
     * @return WelcomeMessage
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("other_logins")
     * @return array
     */
    public function getOtherLogins() : array
    {
        return $this->otherLogins;
    }

    /**
     * @param array $otherLogins
     * @return WelcomeMessage
     */
    public function setOtherLogins(array $otherLogins = [])
    {
        $this->otherLogins = $otherLogins;

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("message")
     * @return string
     */
    public function getMessage()
    {
        return "Welcome, $this->login!";
    }
}