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
     * @JMS\Groups({"get_tournament", "get_tournament_list", "post_tournament_record", "delete_tournament_unrecord"})
     */
    private $id;

    /**
     * @var Game
     *
     * @ORM\OneToOne(targetEntity="Game", cascade={"persist", "remove"}, mappedBy="tournamentGame")
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
     * @JMS\Groups({"get_tournament", "get_tournament_list", "post_tournament_record", "delete_tournament_unrecord"})
     */
    private $round = 0;

    /**
     * @var TournamentPlayer
     *
     * @ORM\ManyToOne(targetEntity="TournamentPlayer")
     * @ORM\JoinColumn(name="player_white_id", referencedColumnName="id")
     *
     * @JMS\Type("CoreBundle\Entity\TournamentPlayer")
     * @JMS\Exclude()
     */
    private $playerWhite;

    /**
     * @var TournamentPlayer
     *
     * @ORM\ManyToOne(targetEntity="TournamentPlayer")
     * @ORM\JoinColumn(name="player_black_id", referencedColumnName="id")
     *
     * @JMS\Type("CoreBundle\Entity\TournamentPlayer")
     * @JMS\Exclude()
     */
    private $playerBlack;

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

    /**
     * @return TournamentPlayer
     */
    public function getPlayerWhite() : TournamentPlayer
    {
        return $this->playerWhite;
    }

    /**
     * @param TournamentPlayer $playerWhite
     * @return TournamentGame
     */
    public function setPlayerWhite($playerWhite)
    {
        $this->playerWhite = $playerWhite;

        return $this;
    }

    /**
     * @return TournamentPlayer
     */
    public function getPlayerBlack() : TournamentPlayer
    {
        return $this->playerBlack;
    }

    /**
     * @param TournamentPlayer $playerBlack
     * @return TournamentGame
     */
    public function setPlayerBlack($playerBlack)
    {
        $this->playerBlack = $playerBlack;

        return $this;
    }
}