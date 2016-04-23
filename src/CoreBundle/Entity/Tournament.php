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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TournamentPlayer", mappedBy="tournament", cascade={"persist", "remove"})
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
     * @return ArrayCollection
     */
    public function getPlayers() : ArrayCollection
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
    public function setGames(ArrayCollection $games) : Tournament
    {
        $this->games = $games;

        return $this;
    }
}