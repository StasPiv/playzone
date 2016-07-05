<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 23:22
 */

namespace ImmortalchessNetBundle\Service\Event;

use CoreBundle\Model\Chess\PgnGame;
use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Service\Chess\ChessGameService;
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
        $title = $this->getTitle($pgnGame);
        $publishService->publishNewThread(
            new Post(
                self::FORUM_FOR_PROBLEMS,
                null,
                $this->container->getParameter("app_immortalchess.post_username_for_calls"),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                $title,
                $this->container->get("templating")->render(
                    ":Post:fenproblem.html.twig",
                    [
                        "pgnGame" => $pgnGame,
                        "title" => $title
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

    /**
     * @param PgnGame $pgnGame
     * @return string
     */
    private function getTitle(PgnGame $pgnGame) : string
    {
        $chessGameService = new ChessGameService();
        $chessGameService->_parseFen($pgnGame->getFen());

        switch ($chessGameService->toMove()) {
            case 'B':
                $text = 'Ход черных. ';
                break;
            default:
                $text = 'Ход белых. ';
        }

        switch ($pgnGame->getResult()) {
            case '1-0':
                $text .= "Белые выигрывают";
                break;
            case '1/2-1/2':
                $text .= "Ничья";
                break;
            case '0-1':
                $text .= "Черные выигрывают";
                break;
        }

        return $this->container->get("immortalchessnet.service.publish")->convertText($text);
    }

}