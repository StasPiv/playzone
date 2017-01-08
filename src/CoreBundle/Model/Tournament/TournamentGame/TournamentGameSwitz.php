<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 11:20
 */

namespace CoreBundle\Model\Tournament\TournamentGame;

use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameColor;
use JMS\Serializer\Annotation as JMS;

/**
 * Class TournamentGameSwitz
 * @package CoreBundle\Model\Tournament\TournamentGame
 */
class TournamentGameSwitz
{
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $gameId = 0;
    
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $color = 0;
    
    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    private $result = 0;
    
    /**
     * @var User
     *
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Entity\User")
     */
    private $opponent = 0;

    /**
     * @var bool
     */
    private $finished = true;
    /**
     * @var float
     */
    private $opponentRating;

    /**
     * TournamentGameSwitz constructor.
     * @param int $gameId
     * @param string $color
     * @param float $result
     * @param User $opponent
     * @param bool $finished
     * @param float $opponentRating
     */
    public function __construct(
        int $gameId,
        string $color,
        float $result,
        User $opponent,
        bool $finished = true,
        float $opponentRating = 1800
    )
    {
        $this->gameId = $gameId;
        $this->color = $color;
        $this->result = $result;
        $this->opponent = $opponent;
        $this->finished = $finished;
        $this->opponentRating = $opponentRating;
    }

    /**
     * @return int
     */
    public function getGameId(): int
    {
        return $this->gameId;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return float
     */
    public function getResult(): float
    {
        return $this->result;
    }

    /**
     * @return User
     */
    public function getOpponent(): User
    {
        return $this->opponent;
    }

    /**
     * @return boolean
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * @return float
     */
    public function getOpponentRating(): float
    {
        return $this->opponentRating;
    }

}