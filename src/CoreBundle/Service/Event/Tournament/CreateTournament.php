<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 25.05.16
 * Time: 21:30
 */

namespace CoreBundle\Service\Event\Tournament;

use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use CoreBundle\Model\Tournament\TournamentInitializatorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * Class NewTournamentEventCommand
 * @package CoreBundle\Model\Event\Tournament
 */
class CreateTournament implements EventCommandInterface
{
    use ContainerAwareTrait;

    /** @var TournamentInitializatorInterface */
    private $tournamentInitializator;

    /**
     * @param EventInterface $eventModel
     */
    public function setEventModel(EventInterface $eventModel)
    {
        $this->tournamentInitializator = $eventModel;
    }

    /**
     * @return void
     */
    public function run()
    {
        $tournament = $this->tournamentInitializator->initTournament();
        $tournament->getTournamentParams()->setTimeBegin(
            $this->container->get("core.service.date")->getDateTime($this->tournamentInitializator->getTimeBegin())
        );
        $manager = $this->container->get("doctrine")->getManager();

        foreach ($this->tournamentInitializator->getPlayerIds() as $playerId) {
            $tournament->addPlayer(
                $this->container->get('core.handler.user')->getRepository()->find($playerId)
            );
        }

        $manager->persist($tournament);
        $manager->flush();

        $this->container->get("logger")->error("Dispatch event tournament new");
        $this->container->get("event_dispatcher")
             ->dispatch(
                 TournamentEvents::NEW,
                 (new TournamentScheduler())->setTournamentId($tournament->getId())
                     ->setFrequency(
                         $tournament->getTournamentParams()->getTimeBegin()->format("i H d n N Y")
                     )
             );
    }

}