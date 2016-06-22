<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.06.16
 * Time: 17:50
 */

namespace WebsocketServerBundle\Service\Event\Tournament;

use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;

/**
 * Class NewTournamentListener
 * @package WebsocketServerBundle\Service\Event\Tournament
 */
class NewTournamentListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TournamentEvents::NEW => [
                ['onTournamentNew', 30]
            ]
        ];
    }

    /**
     * @param TournamentScheduler $event
     */
    public function onTournamentNew(TournamentScheduler $event)
    {
        $this->container->get("logger")->error(__METHOD__);
        $this->container->get("ws.playzone.client.sender")->sendMessageToWebsocketServer(
            (new PlayzoneMessage())
                ->setScope(PlayzoneClientMessageScope::SEND_TO_USERS)
                ->setMethod(PlayzoneClientMessageMethod::NEW_TOURNAMENT)
                ->setData(
                    [
                        "tournament_id" => $event->getTournamentId()
                    ]
                )
        );
    }
}