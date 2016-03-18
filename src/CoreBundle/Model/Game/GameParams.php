<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 18.03.16
 * Time: 23:19
 */

namespace CoreBundle\Model\Game;

use JMS\Serializer\Annotation as JMS;

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
     * @var GameColor
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeBase;

    /**
     * @var GameColor
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeIncrement;

    /**
     * @var GameColor
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeLimit;

    /**
     * @return GameColor
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param GameColor $color
     * @return GameParams
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return GameColor
     */
    public function getTimeBase()
    {
        return $this->timeBase;
    }

    /**
     * @param GameColor $timeBase
     * @return GameParams
     */
    public function setTimeBase($timeBase)
    {
        $this->timeBase = $timeBase;

        return $this;
    }

    /**
     * @return GameColor
     */
    public function getTimeIncrement()
    {
        return $this->timeIncrement;
    }

    /**
     * @param GameColor $timeIncrement
     * @return GameParams
     */
    public function setTimeIncrement($timeIncrement)
    {
        $this->timeIncrement = $timeIncrement;

        return $this;
    }

    /**
     * @return GameColor
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param GameColor $timeLimit
     * @return GameParams
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }
}