<?php

namespace CoreBundle\Entity;

use CoreBundle\Model\ChatMessage\ChatMessageContainerInterface;
use CoreBundle\Model\Game\GameColor;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GameRepository")
 */
class Game implements ChatMessageContainerInterface
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
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_move", type="datetime")
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
     * @ORM\Column(name="gone_in_rest_white", type="datetime", nullable=true)
     */
    private $goneInRestWhite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gone_in_rest_black", type="datetime", nullable=true)
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
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $mine = false;

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
     * @var ChatMessage[]
     *
     * @JMS\Expose()
     * @JMS\Type("array<CoreBundle\Entity\ChatMessage>")
     * @ORM\ManyToMany(targetEntity="ChatMessage")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JoinTable(name="game_chat_messages",
     *      joinColumns={@JoinColumn(name="game_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="chat_message_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $chatMessages;

    /**
     * @var float
     *
     * @JMS\Expose
     * @JMS\Type("float")
     */
    private $myResult;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $insufficientMaterialWhite = false;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $insufficientMaterialBlack = false;

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
    public function setTimeWhite(int $timeWhite) : Game
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
    public function setTimeBlack(int $timeBlack) : Game
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
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMine()
    {
        return $this->mine;
    }

    /**
     * @param bool $mine
     * @return Game
     */
    public function setMine($mine)
    {
        $this->mine = $mine;

        return $this;
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
     * @return $this
     */
    public function setOpponent($opponent)
    {
        $this->opponent = $opponent;
        return $this;
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

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("move_color")
     * @JMS\Type("string")
     *
     * @return string
     */
    public function getMoveColor() : string
    {
        return $this->getUserWhite() == $this->getUserToMove() ? GameColor::WHITE : GameColor::BLACK;
    }

    /**
     * @param ChatMessage $message
     * @return Game
     */
    public function addChatMessage(ChatMessage $message) : Game
    {
        $this->chatMessages[] = $message;
        return $this;
    }

    /**
     * @return ChatMessage[]
     */
    public function getChatMessages() : array
    {
        return $this->chatMessages;
    }

    /**
     * @return float
     */
    public function getMyResult()
    {
        return $this->myResult;
    }

    /**
     * @param float $myResult
     * @return Game
     */
    public function setMyResult($myResult)
    {
        $this->myResult = $myResult;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInsufficientMaterialWhite() : bool 
    {
        return $this->insufficientMaterialWhite;
    }

    /**
     * @param boolean $insufficientMaterialWhite
     * @return Game
     */
    public function setInsufficientMaterialWhite(bool $insufficientMaterialWhite)
    {
        $this->insufficientMaterialWhite = $insufficientMaterialWhite;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInsufficientMaterialBlack() : bool 
    {
        return $this->insufficientMaterialBlack;
    }

    /**
     * @param boolean $insufficientMaterialBlack
     * @return Game
     */
    public function setInsufficientMaterialBlack(bool $insufficientMaterialBlack)
    {
        $this->insufficientMaterialBlack = $insufficientMaterialBlack;

        return $this;
    }
}

