<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.01.16
 * Time: 22:52
 */

namespace WebsocketServerBundle\Model;

use CoreBundle\Entity\User;
use CoreBundle\Model\Request\SecurityRequestInterface;
use Ratchet\ConnectionInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use WebsocketServerBundle\Exception\PlayzoneServerException;

class WebsocketUser implements SecurityRequestInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Login is required"
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
     *     message = "Token is required"
     * )
     */
    private $token;

    /**
     * @var User
     *
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Entity\User")
     */
    private $playzoneUser;

    /**
     * @var array
     */
    private $gamesToListenMap = [];

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
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
     * @return $this|void
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $login
     * @return string
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
     * @return User
     */
    public function getPlayzoneUser()
    {
        return $this->playzoneUser;
    }

    /**
     * @param User $playzoneUser
     */
    public function setPlayzoneUser(User $playzoneUser)
    {
        $this->playzoneUser = $playzoneUser;
    }

    /**
     * @param $code
     * @throws \Exception
     */
    public function throwException($code)
    {
        throw new PlayzoneServerException($this->getLogin(), $code);
    }

    /**
     * @param $gameId
     */
    public function addGameToListen($gameId)
    {
        $this->gamesToListenMap[$gameId] = $gameId;
    }

    /**
     * @return array
     */
    public function getGamesToListenMap()
    {
        return $this->gamesToListenMap;
    }
}