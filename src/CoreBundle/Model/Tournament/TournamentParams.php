<?php

namespace CoreBundle\Model\Tournament;

use DateTime;
use JMS\Serializer\Annotation as JMS;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 15:08
 */
abstract class TournamentParams
{
    /**
     * @var DateTime
     * 
     * @JMS\Type("DateTime<'U'>")
     */
    private $timeBegin;

    /**
     * @var int
     * 
     * @JMS\Type("integer")
     */
    private $gamesVsOpponent = 1;

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     *
     * @return TournamentType
     */
    abstract public function getType() : TournamentType;

    /**
     * @return DateTime
     */
    public function getTimeBegin() : DateTime
    {
        return $this->timeBegin;
    }

    /**
     * @param DateTime $timeBegin
     * @return TournamentParams
     */
    public function setTimeBegin(DateTime $timeBegin)
    {
        $this->timeBegin = $timeBegin;

        return $this;
    }

    /**
     * @return int
     */
    public function getGamesVsOpponent() : int 
    {
        return $this->gamesVsOpponent;
    }

    /**
     * @param int $gamesVsOpponent
     * @return TournamentParams
     */
    public function setGamesVsOpponent(int $gamesVsOpponent)
    {
        $this->gamesVsOpponent = $gamesVsOpponent;

        return $this;
    }
}