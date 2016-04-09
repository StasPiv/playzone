<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:36
 */

namespace CoreBundle\Entity;

use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\TournamentParams;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
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
     * @ORM\ManyToMany(targetEntity="User", inversedBy="players", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="tournament_player",
     *      joinColumns={@ORM\JoinColumn(name="tournament_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")}
     *      )
     * 
     * @var PersistentCollection
     */
    private $players;

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
     * @var TournamentParams
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Model\Tournament\TournamentParams")
     *
     * @ORM\Column(name="tournament_params", type="object")
     */
    private $tournamentParams;

    /**
     * @var bool
     */
    private $mine;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="tournament", cascade={"persist", "remove"})
     *
     * @var ArrayCollection
     */
    private $games;

    /**
     * Tournament constructor.
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

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
     * @return PersistentCollection
     */
    public function getPlayers() : PersistentCollection
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

    /**
     * @param User $player
     * @return Tournament
     */
    public function removePlayer(User $player) : Tournament
    {
        $this->players->removeElement($player);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMine() : bool
    {
        return !!$this->mine;
    }

    /**
     * @param boolean $mine
     * @return Tournament
     */
    public function setMine(bool $mine)
    {
        $this->mine = $mine;

        return $this;
    }

    /**
     * @return GameParams
     */
    public function getGameParams() : GameParams
    {
        return $this->gameParams;
    }

    /**
     * @param GameParams $gameParams
     * @return Tournament
     */
    public function setGameParams(GameParams $gameParams) : Tournament
    {
        $this->gameParams = $gameParams;

        return $this;
    }

    /**
     * @return TournamentParams
     */
    public function getTournamentParams() : TournamentParams
    {
        return $this->tournamentParams;
    }

    /**
     * @param TournamentParams $tournamentParams
     * @return Tournament
     */
    public function setTournamentParams(TournamentParams $tournamentParams)
    {
        $this->tournamentParams = $tournamentParams;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGames() : ArrayCollection
    {
        return $this->games;
    }

    /**
     * @param ArrayCollection $games
     * @return Tournament
     */
    public function setGames(ArrayCollection $games)
    {
        $this->games = $games;

        return $this;
    }

    /**
     * @param Game $game
     * @return Tournament
     */
    public function addGame(Game $game) : Tournament
    {
        $this->games->add($game);
        $game->setTournament($this);
        return $this;
    }

    /**
     * @param Game $game
     * @return Tournament
     */
    public function removeGame(Game $game) : Tournament
    {
        $this->games->removeElement($game);
        return $this;
    }
}