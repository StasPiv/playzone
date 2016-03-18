<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:08
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CallPostSendRequest extends CallRequest implements SecurityRequestInterface
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
     */
    private $player;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\Choice(choices = {"w", "b", "random"}, message = "Color is incorrect")
     */
    private $color;

    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\Range(
     *      min = 1,
     *      max = 10,
     *      minMessage = "Minimum {{ limit }} games to match",
     *      maxMessage = "Maximum {{ limit }} games to match"
     * )
     *
     * @Assert\NotBlank(
     *      message = "Enter games count"
     * )
     */
    private $gamesCount;

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