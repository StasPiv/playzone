<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.04.16
 * Time: 17:34
 */

namespace CoreBundle\Model\Game;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GameMove
 * @package CoreBundle\Model\Game
 */
class GameMove
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $from;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $to;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $promotion;

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return GameMove
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return GameMove
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param string $promotion
     * @return GameMove
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }
}