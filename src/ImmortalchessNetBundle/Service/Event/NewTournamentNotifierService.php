<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 18.08.16
 * Time: 22:14
 */

namespace ImmortalchessNetBundle\Service\Event;

use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Tournament\TournamentStatus;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ImmortalchessNetBundle\Model\Post;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class NewTournamentNotifierService
 * @package ImmortalchessNetBundle\Service\Event
 */
class NewTournamentNotifierService implements EventCommandInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const THREAD_FOR_3_MINUTES = 30991;
    const THREAD_FOR_5_MINUTES = 30984;

    /**
     * @inheritDoc
     */
    public function run()
    {
        /** @var Tournament[] $newTournaments */
        $newTournaments = $this->container->get('core.handler.tournament')
                               ->getRepository()->findBy([
                                    'status' => TournamentStatus::NEW
                                ]);

        foreach ($newTournaments as $tournament) {
            $timeLeft = $tournament->getTournamentParams()->getTimeBegin()->diff(new \DateTime());

            if ($timeLeft->h <= 1) {
                $this->notifyAboutNewTournament($tournament);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function setEventModel(EventInterface $eventModel)
    {
        // TODO: Implement setEventModel() method.
    }

    /**
     * @param Tournament $tournament
     */
    public function notifyAboutNewTournament(Tournament $tournament)
    {
        $title = $this->container->get("templating")->render(
            'Post/newtournament.topic.html.twig',
            [
                'tournament' => $tournament
            ]
        );
        $postModel = new Post(
            $this->container->getParameter("app_immortalchess.forum_playzone"),
            $tournament->getGameParams()->getTimeBase() === 180000 ?
                self::THREAD_FOR_3_MINUTES : self::THREAD_FOR_5_MINUTES,
            $this->container->getParameter("app_immortalchess.post_username_for_calls"),
            $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
            $title,
            $this->container->get("templating")->render(
                'Post/newtournament.html.twig',
                [
                    'tournament' => $tournament
                ]
            )
        );

        $postModel->setThreadTitle($title);

        return $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            $postModel
        );
    }

}