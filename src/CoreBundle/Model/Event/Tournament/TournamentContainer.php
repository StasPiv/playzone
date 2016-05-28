<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 19:11
 */

namespace CoreBundle\Model\Event\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Tournament\TournamentContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TournamentContainer
 * @package CoreBundle\Model\Tournament
 */
class TournamentContainer extends Event implements TournamentContainerInterface
{
    /**
     * @var Tournament
     */
    private $tournament;

    /**
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return TournamentContainer
     */
    public function setTournament($tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }
    
    /**
     * @return int
     */
    function getTournamentId() : int
    {
        return $this->getTournament()->getId();
    }

}