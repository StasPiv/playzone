<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 11:09
 */

namespace CoreBundle\Model\Tournament\TournamentGame;

use JMS\Serializer\Annotation as JMS;

/**
 * Class TournamentGameRoundRobin
 * @package CoreBundle\Model\Tournament\TournamentGame
 */
class TournamentGameRoundRobin
{
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $gameId = 0;

    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    private $result = 0;

    /**
     * TournamentGameRoundRobin constructor.
     * @param int $gameId
     * @param float $result
     */
    public function __construct(int $gameId, float $result)
    {
        $this->gameId = $gameId;
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param int $gameId
     * @return TournamentGameRoundRobin
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * @return float
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param float $result
     * @return TournamentGameRoundRobin
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}