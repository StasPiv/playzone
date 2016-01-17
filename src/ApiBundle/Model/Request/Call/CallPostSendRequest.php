<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:08
 */

namespace ApiBundle\Model\Request\Call;

use CoreBundle\Model\Game\GameColor;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CallPostSendRequest extends CallRequest
{
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
     *     message = "Enter player"
     * )
     *
     */
    private $player;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $color = GameColor::RANDOM;

    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     *
     * @Assert\NotBlank(
     *     message = "Enter timecontrol"
     * )
     */
    private $timecontrol;

    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $gamesCount = 1;

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
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param string $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getTimecontrol()
    {
        return $this->timecontrol;
    }

    /**
     * @param int $timecontrol
     */
    public function setTimecontrol($timecontrol)
    {
        $this->timecontrol = $timecontrol;
    }

    /**
     * @return int
     */
    public function getGamesCount()
    {
        return $this->gamesCount;
    }

    /**
     * @param int $gamesCount
     */
    public function setGamesCount($gamesCount)
    {
        $this->gamesCount = $gamesCount;
    }
}