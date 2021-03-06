<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 22:32
 */

namespace CoreBundle\Service\Chess;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Service\Chess\Pgn\PgnParser;
use CoreBundle\Model\Chess\PgnGame;
use CoreBundle\Service\Chess\PgnService\GetGameFactory;
use StasPiv\PgnSaver\Model\Pgn;
use StasPiv\PgnSaver\Service\ArrayOfPgnContainer;
use StasPiv\PgnSaver\Service\OneGameContainer;
use StasPiv\PgnSaver\Service\Saver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class PgnService
 * @package CoreBundle\Service\Chess
 */
class PgnService implements ContainerAwareInterface, EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @var GetGameFactory
     */
    private $getGameFactory;

    /**
     * @param GetGameFactory $getGameFactory
     */
    public function __construct(GetGameFactory $getGameFactory)
    {
        $this->getGameFactory = $getGameFactory;
    }

    /**
     * @param string $pgnPath
     * @param string $strategy
     * @return PgnGame
     * @internal param array $params
     */
    public function getPgnGame(
        string $pgnPath,
        string $strategy = 'random'
    ) : PgnGame
    {
        return $this->getGameFactory->create($strategy)->getGame($pgnPath);
    }

    /**
     * @param Game $game
     * @return Pgn
     */
    private function buildPgnForSaver(Game $game): Pgn
    {
        $headers = [
            'Event' => 'Game #'.$game->getId(),
            'Site' => $this->container->getParameter('app_core_site_host'),
            'White' => $game->getUserWhite(),
            'Black' => $game->getUserBlack(),
            'Result' => $game->getResultWhite() == 0.5 ? '1/2-1/2' : $game->getResultWhite().'-'.$game->getResultBlack(),
            'Date' => $game->getTimeLastMove()->format('Y.m.d')
        ];

        if ($game->getTournamentGame()) {
            $headers['Event'] = $game->getTournamentGame()->getTournament()->getName();
            $headers['Round'] = $game->getTournamentGame()->getRound();
        }

        return (new Pgn())->setPgnString($game->getPgn())->setHeaders($headers);
    }

    /**
     * @param User $user
     * @param array $pgnForSaverArray
     */
    public function appendUserGamesToHisPgn(User $user, array $pgnForSaverArray)
    {
        $saver = new Saver(
            new ArrayOfPgnContainer($pgnForSaverArray),
            $this->container->get("core.handler.user")->getPgnFilePath($user)
        );

        $saver->saveToFile();
    }

    /**
     * @param User $user
     * @param Game $game
     */
    public function appendUserGameToHisPgn(User $user, Game $game)
    {
        $saver = new Saver(
            new OneGameContainer($this->buildPgnForSaver($game)),
            $this->container->get("core.handler.user")->getPgnFilePath($user)
        );

        $saver->saveToFile();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GameEvents::CHANGE_STATUS_AFTER => [
                ['onGameChangeStatus', 20]
            ]
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function onGameChangeStatus(GameEvent $event)
    {
        if ($event->getGame()->getStatus() != GameStatus::END) {
            return;
        }

        $this->appendUserGameToHisPgn($event->getGame()->getUserWhite(), $event->getGame());
        $this->appendUserGameToHisPgn($event->getGame()->getUserBlack(), $event->getGame());
    }
}