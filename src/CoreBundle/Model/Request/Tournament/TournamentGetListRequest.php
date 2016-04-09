<?php

namespace CoreBundle\Model\Request\Tournament;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:52
 */
class TournamentGetListRequest extends TournamentRequest implements SecurityRequestInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $login;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $token;

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
}