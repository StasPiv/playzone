<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Timecontrol
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @ORM\Table(name="timecontrol")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\TimecontrolRepository")
 */
class Timecontrol
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @JMS\Expose
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="begin", type="integer")
     */
    private $begin;

    /**
     * @var float
     *
     * @ORM\Column(name="increment", type="float")
     */
    private $increment;

    /**
     * @var int
     *
     * @ORM\Column(name="moves", type="integer", nullable=true)
     */
    private $moves;

    /**
     * @var int
     *
     * @ORM\Column(name="timeLimit", type="integer")
     */
    private $timeLimit;

    /**
     * @var bool
     *
     * @ORM\Column(name="rest", type="boolean")
     */
    private $rest;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Timecontrol
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set begin
     *
     * @param integer $begin
     *
     * @return Timecontrol
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin
     *
     * @return int
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set increment
     *
     * @param float $increment
     *
     * @return Timecontrol
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;

        return $this;
    }

    /**
     * Get increment
     *
     * @return float
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Set moves
     *
     * @param integer $moves
     *
     * @return Timecontrol
     */
    public function setMoves($moves)
    {
        $this->moves = $moves;

        return $this;
    }

    /**
     * Get moves
     *
     * @return int
     */
    public function getMoves()
    {
        return $this->moves;
    }

    /**
     * Set timeLimit
     *
     * @param integer $timeLimit
     *
     * @return Timecontrol
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * Get timeLimit
     *
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * Set rest
     *
     * @param boolean $rest
     *
     * @return Timecontrol
     */
    public function setRest($rest)
    {
        $this->rest = $rest;

        return $this;
    }

    /**
     * Get rest
     *
     * @return bool
     */
    public function getRest()
    {
        return $this->rest;
    }
}

