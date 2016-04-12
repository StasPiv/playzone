<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.04.16
 * Time: 22:11
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Tournament\TournamentDrawInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SwissService
 * @package CoreBundle\Service
 */
class SwissService implements TournamentDrawInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    public function makeDraw(Tournament $tournament, int $round)
    {
        $this->clearRound($tournament, $round);

        $this->createTournamentGame(
            $tournament,
            $round,
            $this->manager->getRepository("CoreBundle:User")->findOneByLogin("User-A"),
            $this->manager->getRepository("CoreBundle:User")->findOneByLogin("User-N")
        );

        $this->createTournamentGame(
            $tournament,
            $round,
            $this->manager->getRepository("CoreBundle:User")->findOneByLogin("User-B"),
            $this->manager->getRepository("CoreBundle:User")->findOneByLogin("User-O")
        );

        $this->manager->flush();
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return void
     */
    private function clearRound(Tournament $tournament, int $round)
    {
        $existingTournamentGames = $this->manager->getRepository("CoreBundle:TournamentGame")
            ->findBy(
                [
                    "round" => $round,
                    "tournament" => $tournament
                ]
            );

        foreach ($existingTournamentGames as $exTournamentGame) {
            $this->manager->remove($exTournamentGame);
        }
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @param User $userWhite
     * @param User $userBlack
     * @return void
     */
    private function createTournamentGame(Tournament $tournament, int $round, User $userWhite, User $userBlack)
    {
        $tournamentGame = new TournamentGame();

        $game = new Game();
        $game->setUserWhite($userWhite)
            ->setUserBlack($userBlack)
            ->setUserToMove($game->getUserWhite())
            ->setStatus(GameStatus::PLAY);

        $tournamentGame->setGame($game)
            ->setTournament($tournament)
            ->setRound($round);

        $this->manager->persist($tournamentGame);
    }
}