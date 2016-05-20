<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.05.16
 * Time: 23:28
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Game
 *
 * @ORM\Table(name="game_moves")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\GameMoveRepository")
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @var Game
     * 
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="moves")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $lag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return GameMove
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return Game
     */
    public function getGame() : Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     * @return GameMove
     */
    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return float
     */
    public function getLag() : float
    {
        return $this->lag;
    }

    /**
     * @param float $lag
     * @return GameMove
     */
    public function setLag(float $lag)
    {
        $this->lag = $lag;

        return $this;
    }
}