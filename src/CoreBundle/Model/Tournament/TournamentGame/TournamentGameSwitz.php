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
     * TournamentGameSwitz constructor.
     * @param int $gameId
     * @param string $color
     * @param float $result
     * @param User $opponent
     */
    public function __construct(int $gameId, string $color, float $result, User $opponent)
    {
        $this->gameId = $gameId;
        $this->color = $color;
        $this->result = $result;
        $this->opponent = $opponent;
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

}