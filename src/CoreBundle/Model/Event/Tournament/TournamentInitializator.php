<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 20:21
 */

namespace CoreBundle\Model\Event\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Event\EventFrequencyAwareTrait;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Tournament\TournamentInitializatorInterface;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\TournamentParams;
use CoreBundle\Model\Tournament\TournamentType;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewTournamentEvent
 * @package CoreBundle\Model\Event
 */
class TournamentInitializator extends Event implements TournamentInitializatorInterface, EventInterface
{
    use EventFrequencyAwareTrait;
    
    /**
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @var string
     */
    private $tournamentName;

    /**
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Model\Game\GameParams")
     *
     * @var GameParams
     */
    private $gameParams;

    /**
     * @JMS\Expose()
     * @JMS\Type("CoreBundle\Model\Tournament\TournamentParams")
     *
     * @var TournamentParams
     */
    private $tournamentParams;

    /**
     * @var string
     */
    private $timeBegin;

    /**
     * @var TournamentType
     */
    private $tournamentType;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->getTournamentName();
    }

    /**
     * @return string
     */
    public function getTournamentName()
    {
        return $this->tournamentName;
    }

    /**
     * @param string $tournamentName
     * @return TournamentInitializator
     */
    public function setTournamentName($tournamentName)
    {
        $this->tournamentName = $tournamentName;

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
     * @return TournamentInitializator
     */
    public function setGameParams($gameParams)
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
     * @return TournamentInitializator
     */
    public function setTournamentParams($tournamentParams)
    {
        $this->tournamentParams = $tournamentParams;

        return $this;
    }

    /**
     * @return Tournament
     */
    public function initTournament() : Tournament
    {
        return (new Tournament())->setName($this->getTournamentName())
            ->setTournamentParams(
                $this->getTournamentParams()
                     ->setTimeBegin(new \DateTime($this->getTimeBegin()))
            )
            ->setGameParams($this->getGameParams());
    }

    /**
     * @return string
     */
    public function getTimeBegin() : string 
    {
        return $this->timeBegin;
    }

    /**
     * @param string $timeBegin
     * @return TournamentInitializator
     */
    public function setTimeBegin(string $timeBegin)
    {
        $this->timeBegin = $timeBegin;

        return $this;
    }
}