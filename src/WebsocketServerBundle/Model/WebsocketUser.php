<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.01.16
 * Time: 22:52
 */

namespace WebsocketServerBundle\Model;

use CoreBundle\Entity\User;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use Ratchet\ConnectionInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use WebsocketServerBundle\Exception\PlayzoneServerException;

/**
 * Class WebsocketUser
 * @package WebsocketServerBundle\Model
 */
class WebsocketUser implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;
    
    /**
     * @var ConnectionInterface
     */
    private $connection;

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