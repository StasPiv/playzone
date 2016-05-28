<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.04.16
 * Time: 12:29
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\Call\CallSend\Time;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GamePostNewrobotRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePostNewrobotRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var Time
     *
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Model\Request\Call\CallSend\Time")
     *
     * @Assert\NotBlank(
     *     message="Time is required for this request"
     * )
     */
    private $time;

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
     * @return Time
     */
    public function getTime() : Time
    {
        return $this->time;
    }

    /**
     * @param Time $time
     * @return GamePostNewrobotRequest
     */
    public function setTime(Time $time) : GamePostNewrobotRequest
    {
        $this->time = $time;

        return $this;
    }
}