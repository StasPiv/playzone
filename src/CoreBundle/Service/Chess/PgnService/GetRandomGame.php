<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.04.17
 * Time: 11:34
 */

namespace CoreBundle\Service\Chess\PgnService;

use CoreBundle\Service\Chess\Pgn\PgnParser;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class GetRandomGame
 * @package CoreBundle\Service\Chess\PgnService
 */
class GetRandomGame implements GetGameInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var int
     */
    private $forumForRandomProblems;

    /**
     * @param ObjectManager $manager
     * @param int $forumForRandomProblems
     */
    public function __construct(ObjectManager $manager, int $forumForRandomProblems)
    {
        $this->manager = $manager;
        $this->forumForRandomProblems = $forumForRandomProblems;
    }

    /**
     * @inheritDoc
     */
    public function getGame(string $pgnPath)
    {
        $pgnParser = new PgnParser($pgnPath);
        $availableGames = [];

        foreach ($pgnParser->getGames() as $index => $pgnGame) {
            if (!in_array($pgnGame->getFen(), $this->getAlreadyPostedFens())) {
                $availableGames[] = $pgnGame;
            }
        }

        if (empty($availableGames)) {
            throw new NotFoundResourceException;
        }

        return $availableGames[mt_rand(0, count($availableGames) - 1)];
    }

    /**
     * @return array
     */
    private function getAlreadyPostedFens() : array
    {
        $fens = [];

        $threads = $this->manager->getRepository("ImmortalchessNetBundle:Thread")->findBy([
            "forumid" => $this->forumForRandomProblems
        ]);

        foreach ($threads as $thread) {
            $fens[] = $thread->getTaglist();
        }

        return $fens;
    }

}