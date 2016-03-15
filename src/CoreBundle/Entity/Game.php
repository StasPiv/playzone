<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("user_white")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_white", referencedColumnName="id", nullable=false)
     */
    private $userWhite;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("user_black")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_black", referencedColumnName="id", nullable=false)
     */
    private $userBlack;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("user_to_move")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_to_move", referencedColumnName="id", nullable=false)
     */
    private $userToMove;

    /**
     * @var string
     *
     * @ORM\Column(name="pgn", type="text")
     */
    private $pgn = "";

    /**
     * @var string
     *
     * @ORM\Column(name="pgn_alt", type="text")
     */
    private $pgnAlt = "";

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="rate", type="boolean")
     */
    private $rate = false;

    /**
     * @var float
     *
     * @ORM\Column(name="result_white", type="float", nullable=true)
     */
    private $resultWhite;

    /**
     * @var float
     *
     * @ORM\Column(name="result_black", type="float", nullable=true)
     */
    private $resultBlack;

    /**
     * @var int
     *
     * @ORM\Column(name="time_white", type="integer")
     */
    private $timeWhite = 180000;

    /**
     * @var int
     *
     * @ORM\Column(name="time_black", type="integer")
     */
    private $timeBlack = 180000;

    /**
     * @var Timecontrol
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\Timecontrol")
     * @JMS\SerializedName("timecontrol")
     *
     * @ORM\ManyToOne(targetEntity="Timecontrol", fetch="EAGER")
     * @ORM\JoinColumn(name="id_timecontrol", referencedColumnName="id", nullable=false)
     */
    private $timecontrol;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_move", type="time")
     */
    private $timeLastMove;

    /**
     * @var bool
     *
     * @ORM\Column(name="time_over", type="boolean")
     */
    private $timeOver = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gone_in_rest_white", type="time", nullable=true)
     */
    private $goneInRestWhite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gone_in_rest_black", type="time", nullable=true)
     */
    private $goneInRestBlack;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\SerializedName("color")
     * @JMS\Type("string")
     */
    private $color;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $userMove;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     */
    private $opponent;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\SerializedName("draw")
     * @JMS\Type("string")
     *
     * @ORM\Column(type="text", length=1, nullable=true)
     */
    private $draw;

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
     * @return User
     */
    public function getUserWhite()
    {
        return $this->userWhite;
    }

    /**
     * @param User $userWhite
     * @return $this
     */
    public function setUserWhite($userWhite)
    {
        $this->userWhite = $userWhite;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserBlack()
    {
        return $this->userBlack;
    }

    /**
     * @param User $userBlack
     * @return $this
     */
    public function setUserBlack($userBlack)
    {
        $this->userBlack = $userBlack;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserToMove()
    {
        return $this->userToMove;
    }

    /**
     * @param User $userToMove
     * @return $this
     */
    public function setUserToMove($userToMove)
    {
        $this->userToMove = $userToMove;

        return $this;
    }

    /**
     * Set pgn
     *
     * @param string $pgn
     *
     * @return Game
     */
    public function setPgn($pgn)
    {
        $this->pgn = $pgn;

        return $this;
    }

    /**
     * Get pgn
     *
     * @return string
     */
    public function getPgn()
    {
        return $this->pgn;
    }

    /**
     * Set pgnAlt
     *
     * @param string $pgnAlt
     *
     * @return Game
     */
    public function setPgnAlt($pgnAlt)
    {
        $this->pgnAlt = $pgnAlt;

        return $this;
    }

    /**
     * Get pgnAlt
     *
     * @return string
     */
    public function getPgnAlt()
    {
        return $this->pgnAlt;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set rate
     *
     * @param boolean $rate
     *
     * @return Game
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return bool
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set resultWhite
     *
     * @param float $resultWhite
     *
     * @return Game
     */
    public function setResultWhite($resultWhite)
    {
        $this->resultWhite = $resultWhite;

        return $this;
    }

    /**
     * Get resultWhite
     *
     * @return float
     */
    public function getResultWhite()
    {
        return $this->resultWhite;
    }

    /**
     * Set resultBlack
     *
     * @param float $resultBlack
     *
     * @return Game
     */
    public function setResultBlack($resultBlack)
    {
        $this->resultBlack = $resultBlack;

        return $this;
    }

    /**
     * Get resultBlack
     *
     * @return float
     */
    public function getResultBlack()
    {
        return $this->resultBlack;
    }

    /**
     * Set timeWhite
     *
     * @param integer $timeWhite
     *
     * @return Game
     */
    public function setTimeWhite($timeWhite)
    {
        $this->timeWhite = $timeWhite;

        return $this;
    }

    /**
     * Get timeWhite
     *
     * @return int
     */
    public function getTimeWhite()
    {
        return $this->timeWhite;
    }

    /**
     * Set timeBlack
     *
     * @param integer $timeBlack
     *
     * @return Game
     */
    public function setTimeBlack($timeBlack)
    {
        $this->timeBlack = $timeBlack;

        return $this;
    }

    /**
     * Get timeBlack
     *
     * @return int
     */
    public function getTimeBlack()
    {
        return $this->timeBlack;
    }

    /**
     * @return Timecontrol
     */
    public function getTimecontrol()
    {
        return $this->timecontrol;
    }

    /**
     * @param Timecontrol $timecontrol
     * @return $this
     */
    public function setTimecontrol($timecontrol)
    {
        $this->timecontrol = $timecontrol;

        return $this;
    }

    /**
     * Set timeLastMove
     *
     * @param \DateTime $timeLastMove
     *
     * @return Game
     */
    public function setTimeLastMove($timeLastMove)
    {
        $this->timeLastMove = $timeLastMove;

        return $this;
    }

    /**
     * Get timeLastMove
     *
     * @return \DateTime
     */
    public function getTimeLastMove()
    {
        return $this->timeLastMove;
    }

    /**
     * Set timeOver
     *
     * @param boolean $timeOver
     *
     * @return Game
     */
    public function setTimeOver($timeOver)
    {
        $this->timeOver = $timeOver;

        return $this;
    }

    /**
     * Get timeOver
     *
     * @return bool
     */
    public function getTimeOver()
    {
        return $this->timeOver;
    }

    /**
     * Set goneInRestWhite
     *
     * @param \DateTime $goneInRestWhite
     *
     * @return Game
     */
    public function setGoneInRestWhite($goneInRestWhite)
    {
        $this->goneInRestWhite = $goneInRestWhite;

        return $this;
    }

    /**
     * Get goneInRestWhite
     *
     * @return \DateTime
     */
    public function getGoneInRestWhite()
    {
        return $this->goneInRestWhite;
    }

    /**
     * Set goneInRestBlack
     *
     * @param \DateTime $goneInRestBlack
     *
     * @return Game
     */
    public function setGoneInRestBlack($goneInRestBlack)
    {
        $this->goneInRestBlack = $goneInRestBlack;

        return $this;
    }

    /**
     * Get goneInRestBlack
     *
     * @return \DateTime
     */
    public function getGoneInRestBlack()
    {
        return $this->goneInRestBlack;
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
     * @return boolean
     */
    public function isUserMove()
    {
        return $this->userMove;
    }

    /**
     * @param boolean $userMove
     */
    public function setUserMove($userMove)
    {
        $this->userMove = $userMove;
    }

    /**
     * @return User
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * @param User $opponent
     */
    public function setOpponent($opponent)
    {
        $this->opponent = $opponent;
    }

    /**
     * @return mixed
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * @param mixed $draw
     * @return Game
     */
    public function setDraw($draw)
    {
        $this->draw = $draw;

        return $this;
    }
}

