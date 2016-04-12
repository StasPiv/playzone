<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 10.04.16
 * Time: 22:25
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class TournamentGame
 * @package CoreBundle\Entity
 *
 * @ORM\Table(name="tournament_game")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\TournamentGameRepository")
 */
class TournamentGame
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
     * @ORM\OneToOne(targetEntity="Game", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     *
     * @JMS\Type("CoreBundle\Entity\Game")
     */
    private $game;

    /**
     * @var Tournament
     *
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="games")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     *
     * @JMS\Type("CoreBundle\Entity\Tournament")
     */
    private $tournament;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * 
     * @JMS\Type("integer")
     */
    private $round = 0;

    /**
     * @return int
     */
    public function getId() : int 
    {
        return $this->id;
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
     * @return TournamentGame
     */
    public function setGame(Game $game) : TournamentGame
    {
        $this->game = $game;

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
     * @return TournamentGame
     */
    public function setTournament(Tournament $tournament) : TournamentGame
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * @return int
     */
    public function getRound() : int 
    {
        return $this->round;
    }

    /**
     * @param int $round
     * @return TournamentGame
     */
    public function setRound(int $round) : TournamentGame
    {
        $this->round = $round;

        return $this;
    }
}