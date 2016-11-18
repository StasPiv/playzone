<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 20:49
 */

namespace CoreBundle\Entity;

use CoreBundle\Model\Game\GameColor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * Class TournamentPlayer
 *
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
     *
     * @JMS\Exclude()
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
     * @var float
     * 
     * @ORM\Column(type="float")
     */
    private $points = 0;

    /**
     * @var float
     *
     * @JMS\Exclude()
     */
    private $pointsForDraw = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="count_white")
     *
     * @JMS\Exclude()
     */
    private $countWhite = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="count_black")
     *
     * @JMS\Exclude()
     */
    private $countBlack = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="white_in_row")
     *
     * @JMS\Exclude()
     */
    private $whiteInRow = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="black_in_row")
     *
     * @JMS\Exclude()
     */
    private $blackInRow = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="missed_round")
     *
     * @JMS\Exclude()
     */
    private $missedRound = false;

    /**
     * @ORM\Column(type="array")
     *
     * @var array
     *
     * @JMS\Exclude()
     */
    private $opponents = [];

    /**
     * @var string
     *
     * @JMS\Exclude()
     */
    private $requiredColor;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @JMS\Groups({"get_tournament"})
     */
    private $coefficient = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

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
     * @return float
     */
    public function getPoints() : float
    {
        return $this->points;
    }

    /**
     * @return float
     */
    public function getPointsForDraw() : float 
    {
        return $this->pointsForDraw;
    }

    /**
     * @param float $pointsForDraw
     * @return TournamentPlayer
     */
    public function setPointsForDraw(float $pointsForDraw) : TournamentPlayer
    {
        $this->pointsForDraw = $pointsForDraw;

        return $this;
    }

    /**
     * @param float $points
     * @return TournamentPlayer
     */
    public function setPoints(float $points) : TournamentPlayer
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountWhite() : int 
    {
        return $this->countWhite;
    }

    /**
     * @param int $countWhite
     * @return TournamentPlayer
     */
    public function setCountWhite($countWhite) : TournamentPlayer
    {
        $this->countWhite = $countWhite;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountBlack() : int 
    {
        return $this->countBlack;
    }

    /**
     * @param int $countBlack
     * @return TournamentPlayer
     */
    public function setCountBlack($countBlack) : TournamentPlayer
    {
        $this->countBlack = $countBlack;

        return $this;
    }

    /**
     * @return int
     */
    public function getWhiteInRow() : int 
    {
        return $this->whiteInRow;
    }

    /**
     * @param int $whiteInRow
     * @return TournamentPlayer
     */
    public function setWhiteInRow($whiteInRow) : TournamentPlayer
    {
        $this->whiteInRow = $whiteInRow;

        return $this;
    }

    /**
     * @return int
     */
    public function getBlackInRow() : int 
    {
        return $this->blackInRow;
    }

    /**
     * @param int $blackInRow
     * @return TournamentPlayer
     */
    public function setBlackInRow($blackInRow) : TournamentPlayer
    {
        $this->blackInRow = $blackInRow;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMissedRound() : bool 
    {
        return $this->missedRound;
    }

    /**
     * @param boolean $missedRound
     * @return TournamentPlayer
     */
    public function setMissedRound($missedRound) : TournamentPlayer
    {
        $this->missedRound = $missedRound;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addOpponent(User $user)
    {
        if (!$this->opponents) {
            $this->opponents = [];
        }
        
        $this->opponents[] = $user->getId();
        return $this;
    }

    /**
     * @return array
     */
    public function getOpponents() : array 
    {
        return $this->opponents;
    }

    /**
     * @param string $requiredColor
     * @return TournamentPlayer
     */
    public function setRequiredColor(string $requiredColor)
    {
        $this->requiredColor = $requiredColor;

        return $this;
    }

    /**
     * @return string
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("required_color")
     */
    public function getRequiredColor()
    {
        if ($this->requiredColor) {
            return $this->requiredColor;
        }

        switch (true) {
            case $this->getBlackInRow() > 1:
                return GameColor::WHITE;
            case $this->getWhiteInRow() > 1:
                return GameColor::BLACK;
            case $this->getCountBlack() > $this->getCountWhite() + 1:
                return GameColor::WHITE;
            case $this->getCountWhite() > $this->getCountBlack() + 1:
                return GameColor::BLACK;
            default:
                return GameColor::RANDOM;
        }
    }

    /**
     * @return float
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * @param float $coefficient
     * @return TournamentPlayer
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return TournamentPlayer
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
    
    
}