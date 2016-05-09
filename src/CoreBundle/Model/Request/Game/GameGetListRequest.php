<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:21
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Call\CallType;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\User\UserType;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class GameGetListRequest extends GameRequest implements SecurityRequestInterface
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $login;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $token;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $user;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $status;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $callType;

    /**
     * @var integer
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $limit = 100;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return GameGetListRequest
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCallType()
    {
        return $this->callType;
    }

    /**
     * @param mixed $callType
     */
    public function setCallType($callType)
    {
        $this->callType = $callType;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return GameGetListRequest
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
}