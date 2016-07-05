<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 23:22
 */

namespace ImmortalchessNetBundle\Service\Event;

use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use ImmortalchessNetBundle\Model\Post;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PostProblem
 * @package ImmortachessNetBundle\Service\Event
 */
class PostProblemService implements EventCommandInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    const FORUM_FOR_PROBLEMS = 147;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $pgnGame = $this->container->get("core.service.chess.pgn")->getRandomPgn(
            $this->container->get("kernel")->getRootDir() . DIRECTORY_SEPARATOR . '../web/uploads/korol.pgn'         );

        $publishService = $this->container->get("immortalchessnet.service.publish");
        $publishService->publishNewThread(
            new Post(
                self::FORUM_FOR_PROBLEMS,
                null,
                $this->container->getParameter("app_immortalchess.post_username_for_calls"),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                $pgnGame->getResult() == "1-0" ? $publishService->convertText("Белые выигрывают") :
                    $publishService->convertText("Черные выигрывают"),
                $this->container->get("templating")->render(
                    ":Post:fenproblem.html.twig",
                    [
                        "pgnGame" => $pgnGame
                    ]
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function setEventModel(EventInterface $eventModel)
    {
        // TODO: Implement setEventModel() method.
    }

}