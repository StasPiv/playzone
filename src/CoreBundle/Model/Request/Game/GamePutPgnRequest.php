<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 15:45
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class GamePutPgnRequest extends GameRequest implements SecurityRequestInterface
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
     * @JMS\Type("string")
     */
    private $pgn;

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
     * @return string
     */
    public function getPgn()
    {
        return $this->pgn;
    }

    /**
     * @param string $pgn
     * @return GamePutPgnRequest
     */
    public function setPgn($pgn)
    {
        $this->pgn = $pgn;

        return $this;
    }
}