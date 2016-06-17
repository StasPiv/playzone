<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.16
 * Time: 22:49
 */

namespace ImmortalchessNetBundle\Service;

use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Model\Event\Call\CallEvent;
use CoreBundle\Model\Event\Call\CallEvents;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Event\Game\GamePublishEvent;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use CoreBundle\Model\Event\User\UserAuthEvent;
use CoreBundle\Model\Event\User\UserEvents;
use CoreBundle\Model\Game\GameColor;
use ImmortalchessNetBundle\Model\Post;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ImmortalchessnetService
 * @package CoreBundle\Service
 */
class ImmortalchessnetService implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    const THREAD_FOR_3_MINUTES = 30991;
    const THREAD_FOR_5_MINUTES = 30984;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TournamentEvents::NEW => [
                ['onTournamentNew', 20]
            ],
            CallEvents::NEW_CALL => [
                ['onNewCall', 10]
            ],
            UserEvents::USER_AUTH => [
                ['onUserAuth', 10]
            ],
            GameEvents::PUBLISH_FEN => [
                ['onGamePublishFen', 10]
            ],
            GameEvents::PUBLISH_PGN => [
                ['onGamePublishPgn', 10]
            ]
        ];
    }

    /**
     * @param GamePublishEvent $event
     */
    public function onGamePublishFen(GamePublishEvent $event)
    {
        $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                31003,
                $event->getUser()->getLogin(),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                "Позиция из партии " . $event->getGame()->getUserWhite() . "-" . $event->getGame()->getUserBlack(),
                $this->container->get("templating")->render(
                    'Post/gamefen.html.twig',
                    [
                        'game' => $event->getGame(),
                        'fen' => $event->getFen()
                    ]
                )
            )
        );
    }

    /**
     * @param GamePublishEvent $event
     */
    public function onGamePublishPgn(GamePublishEvent $event)
    {
        $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                31003,
                $event->getUser()->getLogin(),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                "Партия " . $event->getGame()->getUserWhite() . "-" . $event->getGame()->getUserBlack(),
                $this->container->get("templating")->render(
                    'Post/gamepgn.html.twig',
                    [
                        'game' => $event->getGame(),
                        'pgn' => $event->getPgn()
                    ]
                )
            )
        );
    }

    /**
     * @param CallEvent $event
     */
    public function onNewCall(CallEvent $event)
    {
        $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                $this->container->getParameter("app_immortalchess.thread_for_calls"),
                $event->getCall()->getFromUser()->getLogin(),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                'Новый вызов от ' . $event->getCall()->getFromUser()->getLogin(),
                $this->container->get("templating")->render(
                    'Post/newcall.html.twig',
                    [
                        'user' => $event->getCall()->getFromUser(),
                        'time_minutes' => $event->getCall()->getGameParams()->getTimeBase() / 60000,
                        'color' => GameColor::getOppositeColor($event->getCall()->getGameParams()->getColor())
                    ]
                )
            )
        );
    }

    /**
     * @param TournamentScheduler $event
     */
    public function onTournamentNew(TournamentScheduler $event)
    {
        try {
            $tournament = $this->container->get("core.handler.tournament")
                ->getRepository()->find($event->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            return;
        }

        $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                $tournament->getGameParams()->getTimeBase() === 180000 ?
                    self::THREAD_FOR_3_MINUTES : self::THREAD_FOR_5_MINUTES,
                "PozitiFF_Chess",
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                "Запись в турнир открыта",
                $this->container->get("templating")->render(
                    'Post/newtournament.html.twig',
                    [
                        'tournament' => $tournament
                    ]
                )
            )
        );
    }

    /**
     * @param UserAuthEvent $event
     */
    public function onUserAuth(UserAuthEvent $event)
    {
        $user = $this->container->get("immortalchessnet.service.user")
                     ->getUser(
                        $event->getLogin(), $event->getPassword()
                     )
                     ->setPassword(
                        $this->container->get("core.handler.user")
                             ->generatePasswordHash($event->getPassword())
                     );

        $event->setUser($user);
    }
}