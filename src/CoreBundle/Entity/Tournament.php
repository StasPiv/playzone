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
use CoreBundle\Model\Tournament\TournamentStatus;
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TournamentPlayer", mappedBy="tournament", cascade={"persist", "remove"})
     * @ORM\OrderBy({"points" = "DESC"})
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TournamentGame", mappedBy="tournament", cascade={"persist", "remove"})
     */
    private $games;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     */
    private $currentRound = 0;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     */
    private $rounds = 0;

    /**
     * @var TournamentStatus
     * 
     * @ORM\Column(type="string")
     */
    private $status = TournamentStatus::NEW;

    /**
     * @var Game[]
     *
     * @JMS\Expose()
     * @JMS\Type("array<CoreBundle\Entity\Game>")
     */
    private $allGames;

    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    private $resultsForRoundRobin;

    /**
     * Tournament constructor.
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->players = new ArrayCollection();
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
     * @return ArrayCollection|TournamentPlayer[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param User $player
     * @return Tournament
     */
    public function addPlayer(User $player) : Tournament
    {
        $tournamentPlayer = new TournamentPlayer();
        $tournamentPlayer->setTournament($this)
                         ->setPlayer($player);
        $this->players->add($tournamentPlayer);
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
     * @return PersistentCollection|TournamentGame[]
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param ArrayCollection $games
     * @return Tournament
     */
    public function setGames(ArrayCollection $games) : Tournament
    {
        $this->games = $games;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentRound() : int
    {
        return $this->currentRound;
    }

    /**
     * @param int $currentRound
     * @return Tournament
     */
    public function setCurrentRound(int $currentRound)
    {
        $this->currentRound = $currentRound;

        return $this;
    }

    /**
     * @return TournamentStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param TournamentStatus $status
     * @return Tournament
     */
    public function setStatus(TournamentStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getRounds() : int 
    {
        return $this->rounds;
    }

    /**
     * @param int $rounds
     * @return Tournament
     */
    public function setRounds(int $rounds)
    {
        $this->rounds = $rounds;

        return $this;
    }

    /**
     * @return Game[]
     */
    public function getAllGames()
    {
        return $this->allGames;
    }

    /**
     * @param Game[] $allGames
     * @return Tournament
     */
    public function setAllGames($allGames)
    {
        $this->allGames = $allGames;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getResultsForRoundRobin()
    {
        return $this->resultsForRoundRobin;
    }

    /**
     * @param int[] $resultsForRoundRobin
     * @return Tournament
     */
    public function setResultsForRoundRobin($resultsForRoundRobin)
    {
        $this->resultsForRoundRobin = $resultsForRoundRobin;

        return $this;
    }
}