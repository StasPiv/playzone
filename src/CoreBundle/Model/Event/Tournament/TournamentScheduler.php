<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.05.16
 * Time: 0:03
 */

namespace CoreBundle\Model\Event\Tournament;

use CoreBundle\Model\Event\EventFrequencyAwareTrait;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Tournament\TournamentContainerInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TournamentContainer
 * @package CoreBundle\Model\Event\Tournament
 */
class TournamentScheduler extends Event implements EventInterface, TournamentContainerInterface
{
    use EventFrequencyAwareTrait;

    /**
     * @JMS\Expose()
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $tournamentId;

    /**
     * @return int
     */
    public function getTournamentId() : int
    {
        return $this->tournamentId;
    }

    /**
     * @param int $tournamentId
     * @return TournamentScheduler
     */
    public function setTournamentId(int $tournamentId)
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return "Tournament";
    }
}