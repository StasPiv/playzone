<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.04.16
 * Time: 17:23
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GameGetRobotmoveAction
 * @package CoreBundle\Model\Request\Game
 */
class GameGetRobotmoveAction extends GameRequest implements SecurityRequestInterface
{
    /**
     * @var int
     *
     * @JMS\Type("integer")
     */
    private $id;

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
     *     message = "Fen is required for this request"
     * )
     */
    private $encodedFen;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GameGetRequest
     */
    public function setId($id)
    {
        $this->id = $id;

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
    public function getEncodedFen() : string 
    {
        return $this->encodedFen;
    }

    /**
     * @param string $encodedFen
     * @return GameGetRobotmoveAction
     */
    public function setEncodedFen($encodedFen)
    {
        $this->encodedFen = $encodedFen;

        return $this;
    }
}