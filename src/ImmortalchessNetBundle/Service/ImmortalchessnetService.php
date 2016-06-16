<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.16
 * Time: 22:49
 */

namespace ImmortalchessNetBundle\Service;

use CoreBundle\Model\Event\Call\CallEvent;
use CoreBundle\Model\Event\Call\CallEvents;
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

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CallEvents::NEW_CALL => [
                ['onNewCall', 10]
            ],
            UserEvents::USER_AUTH => [
                ['onUserAuth', 10]
            ]
        ];
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
                $this->container->getParameter("app_immortalchess.first_post_for_calls"),
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
     * @param UserAuthEvent $event
     */
    public function onUserAuth(UserAuthEvent $event)
    {
        $user = $this->container->get("immortalchessnet.service.user")->getUser($event->getLogin(), $event->getPassword())
                     ->setPassword(
                        $this->container->get("core.handler.user")
                             ->generatePasswordHash($event->getPassword())
                     );

        $event->setUser($user);
    }
}