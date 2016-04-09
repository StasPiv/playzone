<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 20:49
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class TournamentPlayer
 * @package CoreBundle\Entity
 * 
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\TournamentPlayerRepository")
 * @ORM\Table(name="tournament_player")
 */
class TournamentPlayer
{
    /**
     * @var int
     *
     * @JMS\Exclude()
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Tournament
     * 
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="players")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
     */
    private $tournament;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tournaments")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false)
     */
    private $player;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     */
    private $points = 0;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TournamentPlayer
     */
    public function setId(int $id) : TournamentPlayer
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Tournament
     */
    public function getTournament() : Tournament
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return TournamentPlayer
     */
    public function setTournament(Tournament $tournament) : TournamentPlayer
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * @return User
     */
    public function getPlayer() : User
    {
        return $this->player;
    }

    /**
     * @param User $player
     * @return TournamentPlayer
     */
    public function setPlayer(User $player) : TournamentPlayer
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return int
     */
    public function getPoints() : int 
    {
        return $this->points;
    }

    /**
     * @param int $points
     * @return TournamentPlayer
     */
    public function setPoints(int $points) : TournamentPlayer
    {
        $this->points = $points;

        return $this;
    }
    
    
}