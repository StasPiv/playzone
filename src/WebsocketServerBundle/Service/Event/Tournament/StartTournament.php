<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 25.05.16
 * Time: 21:30
 */

namespace WebsocketServerBundle\Service\Event\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentContainerInterface;
use CoreBundle\Model\Tournament\TournamentType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\Client\Tournament\TournamentMesssageNewRound;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use JMS\Serializer\Annotation as JMS;
use WebsocketServerBundle\Service\Client\PlayzoneClientSender;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * Class NewTournamentEventCommand
 * @package CoreBundle\Model\Event\Tournament
 */
class StartTournament implements EventCommandInterface, EventSubscriberInterface
{
    use ContainerAwareTrait;

    /** @var TournamentContainerInterface */
    private $tournamentContainer;
    
    /** @var int */
    private $tournamentId;

    /**
     * @param EventInterface $tournamentContainer
     */
    public function setEventModel(EventInterface $tournamentContainer)
    {
        $this->tournamentContainer = $tournamentContainer;
    }

    /**
     * @return void
     */
    public function run()
    {
        $tournament = $this->container->get("core.handler.tournament")
                           ->getRepository()->find($this->tournamentContainer->getTournamentId());
        
        // TODO: remove all offline players
        
        if (
            $tournament->getCurrentRound() == 0 && 
            count($tournament->getPlayers()) > $this->container->getParameter("app_max_players_for_round_robin")
        ) {
            $this->changeTournamentTypeOnSwiss($tournament);
        }

        $this->container->get("core.service.draw.factory")
             ->create($tournament)
             ->makeDrawForNextRound($tournament);

        $this->sendMessageToWebsocketServer($tournament);
    }

    /**
     * @param Tournament $tournament
     * @throws \WebSocket\BadOpcodeException
     */
    private function sendMessageToWebsocketServer(Tournament $tournament)
    {
        $client = $this->container->get("ws.playzone.client");
        
        $this->getPlayzoneClientSender()->sendIntroductionFromRobot($client);

        $this->getPlayzoneClientSender()->send(
            $client,
            (new PlayzoneMessage())
                ->setMethod(PlayzoneClientMessageMethod::NEW_TOURNAMENT_ROUND)
                ->setScope(PlayzoneClientMessageScope::SEND_TO_USERS)
                ->setData(
                    $this->container->get("core.service.playzone_serializer")->toArray(
                        new TournamentMesssageNewRound($tournament->getId())
                    )
                )
        );
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
     * @return StartTournament
     */
    public function setTournamentId(int $tournamentId)
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }

    /**
     * @param Tournament $tournament
     */
    private function changeTournamentTypeOnSwiss(Tournament $tournament)
    {
        $tournamentParams = TournamentParamsFactory::create(TournamentType::SWITZ())
            ->setTimeBegin($tournament->getTournamentParams()->getTimeBegin());

        $tournament->setTournamentParams($tournamentParams);

        $manager = $this->container->get("doctrine")->getManager();
        $manager->persist($tournament);
        $manager->flush();
    }

    /**
     * @return PlayzoneClientSender
     */
    private function getPlayzoneClientSender()
    {
        return $this->container->get("ws.playzone.client.sender");
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TournamentEvents::TOURNAMENT_NEW => [
                ['onTournamentNew', 10]
            ],
            TournamentEvents::ROUND_FINISHED => [
                ['onRoundFinished', 10]
            ]
        ];
    }

    /**
     * @param TournamentScheduler $tournamentScheduler
     */
    public function onTournamentNew(TournamentScheduler $tournamentScheduler)
    {
        $this->container->get("core.handler.event")->initEventAndSave(
            $tournamentScheduler, "ws.service.event.tournament.start"
        );
    }

    /**
     * @param TournamentScheduler $tournamentScheduler
     */
    public function onRoundFinished(TournamentScheduler $tournamentScheduler)
    {
        // TODO: check if tournament is finished

        $this->container->get("core.handler.event")->initEventAndSave(
            $tournamentScheduler->setFrequency(
                (new \DateTime("+1minute"))->format("i H d n N Y")
            ), "ws.service.event.tournament.start"
        );
    }


}