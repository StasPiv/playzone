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
     * @JMS\Type("DateTime<'d.m.Y H:i:s'>")
     */
    private $timeBegin;

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
}