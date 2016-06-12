<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.06.16
 * Time: 20:17
 */

namespace CoreBundle\Model\Event\User;

use CoreBundle\Model\User\UserContainerAwareTrait;

/**
 * Class UserAuthEvent
 * @package CoreBundle\Model\Event\User
 */
class UserAuthEvent extends UserEvent
{
    /** @var string */
    private $login;
    
    /** @var string */
    private $password;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return UserAuthEvent
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserAuthEvent
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}