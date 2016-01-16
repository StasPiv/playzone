<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * GameCall
 *
 * @ORM\Table(name="game_call")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GameCallRepository")
 */
class GameCall
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("from_user")
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="id_call_from", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $fromUser;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("to_user")
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="id_call_to", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $toUser;

    /**
     * @var Game
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\Game")
     * @JMS\SerializedName("game")
     *
     * @ORM\ManyToOne(targetEntity="Game", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="id_game", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $game;


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
     * Set fromUser
     *
     * @param User $fromUser
     *
     * @return GameCall
     */
    public function setFromUser(User $fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param User $toUser
     *
     * @return GameCall
     */
    public function setToUser(User $toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return User
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return GameCall
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
}

