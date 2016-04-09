<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:36
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Tournament
 * @package CoreBundle\Entity
 *
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\TournamentRepository")
 */
class Tournament
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="features")
     * @ORM\JoinTable(name="tournament_players",
     *      joinColumns={@ORM\JoinColumn(name="tournament_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")}
     *      )
     */
    private $players;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Tournament
     */
    public function setId($id) : Tournament
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tournament
     */
    public function setName(string $name) : Tournament
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getPlayers() : array
    {
        return $this->players;
    }

    /**
     * @param User[] $players
     * @return Tournament
     */
    public function setPlayers(array $players) : Tournament
    {
        $this->players = $players;

        return $this;
    }

    /**
     * @param User $player
     * @return Tournament
     */
    public function addPlayer(User $player) : Tournament
    {
        $this->players[] = $player;
        return $this;
    }
}