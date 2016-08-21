<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 18.03.16
 * Time: 23:19
 */

namespace CoreBundle\Model\Game;

use JMS\Serializer\Annotation as JMS;

/**
 * Class GameParams
 * @package CoreBundle\Model\Game
 */
class GameParams
{
    /**
     * @var GameColor
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $color;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeBase;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeIncrement = 0;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeLimit;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $rate = true;

    /**
     * @return string
     */
    public function getColor() : string
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return GameParams
     */
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeBase() : int
    {
        return $this->timeBase;
    }

    /**
     * @param int $timeBase
     * @return GameParams
     */
    public function setTimeBase(int $timeBase) : GameParams
    {
        $this->timeBase = $timeBase;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeIncrement() : int 
    {
        return $this->timeIncrement;
    }

    /**
     * @param int $timeIncrement
     * @return GameParams
     */
    public function setTimeIncrement(int $timeIncrement) : self 
    {
        $this->timeIncrement = $timeIncrement;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeLimit() : int 
    {
        return $this->timeLimit;
    }

    /**
     * @param int $timeLimit
     * @return GameParams
     */
    public function setTimeLimit(int $timeLimit) : self 
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRate(): bool
    {
        return $this->rate;
    }

    /**
     * @param boolean $rate
     * @return GameParams
     */
    public function setRate(bool $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}