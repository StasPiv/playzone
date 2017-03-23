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
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=true)
     *
     * @JMS\Exclude()
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
     * @ORM\Column(type="datetime")
     *
     * @JMS\Exclude()
     */
    private $timeMove;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $moveNotation;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $moveNumber = 0;

    /**
     * GameMove constructor.
     */
    public function __construct()
    {
        $this->setTimeMove(new \DateTime());
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
    public function getTimeMove(): \DateTime
    {
        return $this->timeMove;
    }

    /**
     * @param \DateTime $timeMove
     * @return GameMove
     */
    public function setTimeMove(\DateTime $timeMove): self
    {
        $this->timeMove = $timeMove;

        return $this;
    }

    /**
     * @return string
     */
    public function getMoveNotation(): string
    {
        return $this->moveNotation;
    }

    /**
     * @param string $moveNotation
     * @return GameMove
     */
    public function setMoveNotation(string $moveNotation): self
    {
        $this->moveNotation = $moveNotation;

        return $this;
    }

    /**
     * @return int
     */
    public function getMoveNumber(): int
    {
        return $this->moveNumber;
    }

    /**
     * @param int $moveNumber
     * @return GameMove
     */
    public function setMoveNumber(int $moveNumber): self
    {
        $this->moveNumber = $moveNumber;

        return $this;
    }
}