<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:08
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\Call\CallSend\Time;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CallPostSendRequest
 * @package CoreBundle\Model\Request\Call
 */
class CallPostSendRequest extends CallRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

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
     * @return string
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param string $player
     * @return $this
     */
    public function setPlayer($player)
    {
        $this->player = $player;
        return $this;
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
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
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
     * @return CallPostSendRequest
     */
    public function setTime(Time $time) : CallPostSendRequest
    {
        $this->time = $time;

        return $this;
    }
}