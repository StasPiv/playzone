<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 08.03.17
 * Time: 13:25
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class GameMove
 * @package CoreBundle\Entity
 *
 * @ORM\Entity()
 */
class GameMove
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
     * @var Game
     *
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     *
     * @JMS\Type("CoreBundle\Entity\Game")
     */
    private $game;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $delay = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_move", type="datetime")
     *
     * @JMS\Exclude()
     */
    private $timeLastMove;

    /**
     * GameMove constructor.
     */
    public function __construct()
    {
        $this->setTimeLastMove(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     * @return GameMove
     */
    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return GameMove
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     * @return GameMove
     */
    public function setDelay(int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeLastMove(): \DateTime
    {
        return $this->timeLastMove;
    }

    /**
     * @param \DateTime $timeLastMove
     * @return GameMove
     */
    public function setTimeLastMove(\DateTime $timeLastMove): self
    {
        $this->timeLastMove = $timeLastMove;

        return $this;
    }
}