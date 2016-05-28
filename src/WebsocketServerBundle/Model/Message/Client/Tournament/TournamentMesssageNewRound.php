<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.05.16
 * Time: 22:56
 */

namespace WebsocketServerBundle\Model\Message\Client\Tournament;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TournamentMesssageNewRound
 * @package WebsocketServerBundle\Model\Message\Client\Tournament
 */
class TournamentMesssageNewRound
{
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $tournamentId;

    /**
     * TournamentMesssageNewRound constructor.
     * @param int $tournamentId
     */
    public function __construct($tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * @return int
     */
    public function getTournamentId() : int
    {
        return $this->tournamentId;
    }

    /**
     * @param int $tournamentId
     * @return TournamentMesssageNewRound
     */
    public function setTournamentId(int $tournamentId) : TournamentMesssageNewRound
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }
}