<?php

namespace CoreBundle\Entity;

use CoreBundle\Model\Game\GameParams;
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
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_call_from", referencedColumnName="id", nullable=false)
     */
    private $fromUser;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("to_user")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_call_to", referencedColumnName="id", nullable=true)
     */
    private $toUser;

    /**
     * @var GameParams
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Model\Game\GameParams")
     * @JMS\SerializedName("game_params")
     *
     * @ORM\Column(name="game_params", type="object")
     */
    private $gameParams;

    /**
     * @var \DateTime
     * 
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * 
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * GameCall constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
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
     * @return GameParams
     */
    public function getGameParams()
    {
        return $this->gameParams;
    }

    /**
     * @param GameParams $gameParams
     * @return GameCall
     */
    public function setGameParams(GameParams $gameParams)
    {
        $this->gameParams = $gameParams;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return GameCall
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

