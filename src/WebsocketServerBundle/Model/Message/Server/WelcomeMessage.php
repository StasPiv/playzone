<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 0:42
 */

namespace WebsocketServerBundle\Model\Message\Server;

use JMS\Serializer\Annotation as JMS;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;

class WelcomeMessage extends PlayzoneMessage
{
    /**
     * @var string
     */
    private $login;

    /**
     * @param $login
     */
    public function __construct($login)
    {
        $this->setLogin($login);
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

    /**
     * @var string
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("message")
     * @return string
     */
    public function getMessage()
    {
        return "Welcome, $this->login!";
    }
}