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
}