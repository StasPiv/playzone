<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 25.05.16
 * Time: 21:30
 */

namespace WebsocketServerBundle\Service\Event\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Exception\Handler\Tournament\TournamentGameNotFoundException;
use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentContainerInterface;
use CoreBundle\Model\Tournament\TournamentStatus;
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
class StartTournamentRound implements EventCommandInterface, EventSubscriberInterface
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
        $tournament = $this->getTournamentHandler()
                           ->getRepository()->find($this->tournamentContainer->getTournamentId());

        if (
            $tournament->getCurrentRound() == 0 && 
            count($tournament->getPlayers()) > $this->container->getParameter("app_max_players_for_round_robin")
        ) {
            $this->changeTournamentTypeOnSwiss($tournament);
        }

        $event = (new TournamentContainer())->setTournament($tournament);
        if ($tournament->getCurrentRound() == 0) {
            $this->container->get("event_dispatcher")->dispatch(
                TournamentEvents::START,
                $event
            );
        }

        $this->container->get("core.service.draw.factory")
             ->create($tournament)
             ->makeDrawForNextRound($tournament);


        $this->container->get("event_dispatcher")->dispatch(
            TournamentEvents::ROUND_START,
            $event
        );

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
     * @return StartTournamentRound
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

        $tournament->setTournamentParams($tournamentParams)->setRounds($this->container->getParameter("rounds_for_swiss"));

        $this->getManager()->persist($tournament);
        $this->getManager()->flush();
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
            TournamentEvents::NEW => [
                ['onTournamentNew', 10]
            ],
            GameEvents::CHANGE_STATUS => [
                ['onGameChangeStatus', 10]
            ]
        ];
    }

    /**
     * @param TournamentScheduler $tournamentScheduler
     */
    public function onTournamentNew(TournamentScheduler $tournamentScheduler)
    {
        $this->container->get("core.handler.event")->initEventAndSave(
            $tournamentScheduler, "ws.service.event.tournament.start_round"
        );
    }

    /**
     * @param GameEvent $gameEvent*/
    public function onGameChangeStatus(GameEvent $gameEvent)
    {
        if ($gameEvent->getGame()->getStatus() != GameStatus::END) {
            return;
        }

        try {
            $tournament = $this->getTournamentHandler()
                               ->getTournamentForGame($gameEvent->getGame());
        } catch (TournamentGameNotFoundException $e) {
            return;
        }
        
        if (!$this->getTournamentHandler()->isCurrentRoundFinished($tournament)) {
            return;
        }

        if ($tournament->getStatus() == TournamentStatus::END()) {
            // TODO: send message to users about finishing tournament
            return;
        }

        $this->container->get("core.handler.event")->initEventAndSave(
            (new TournamentScheduler())
                ->setFrequency(
                    $this->container->get("core.service.date")
                        ->getDateTime("+1minute")->format("i H d n N Y")
                )
                ->setTournamentId($tournament->getId()), 
            "ws.service.event.tournament.start_round"
        );

    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getManager()
    {
        return $this->container->get("doctrine")->getManager();
    }

    /**
     * @return \CoreBundle\Handler\TournamentHandler
     */
    private function getTournamentHandler()
    {
        return $this->container->get("core.handler.tournament");
    }

}