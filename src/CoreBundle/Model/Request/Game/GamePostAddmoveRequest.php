<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.03.16
 * Time: 20:43
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GamePostAddmessageRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePostAddmoveRequest extends GameRequest implements SecurityRequestInterface
{
    /**
     * @var int
     *
     * @JMS\Type("string")
     */
    private $id;

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
     * @JMS\Type("float")
     *
     * @Assert\NotBlank(
     *     message = "Lag is required for this request"
     * )
     */
    private $lag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GamePutPgnRequest
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
     * @return GameGetRequest
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
     * @return GameGetRequest
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return float
     */
    public function getLag() : float 
    {
        return $this->lag;
    }

    /**
     * @param float $lag
     * @return GamePostAddmessageRequest
     */
    public function setLag(float $lag)
    {
        $this->lag = $lag;

        return $this;
    }
}