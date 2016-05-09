<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.05.16
 * Time: 15:50
 */

namespace CoreBundle\Tests\Service\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Service\Tournament\RoundrobinService;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class RoundRobinServiceTest
 * @package CoreBundle\Tests\Service\Tournament
 */
class RoundRobinServiceTest extends KernelAwareTest
{
    /**
     * @var RoundrobinService
     */
    protected $service;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->container->get("core.service.round_robin");
    }

    public function testDraw()
    {
        /** @var Tournament $tournament */
        $tournament = $this->getTournament();

        $countPlayers = count(
            $this->getTournamentHandler()->getPlayers($tournament)
        );

        for ($round=1; $round <= $countPlayers; $round++) {
            $this->service->makeDraw($tournament, $round);

            $this->assertGames($tournament, $round);
        }

    }

    /**
     * @return TournamentHandler
     * @throws \Exception
     */
    private function getTournamentHandler()
    {
        return $this->container->get("core.handler.tournament");
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return array|\CoreBundle\Entity\TournamentGame[]
     */
    private function assertGames(Tournament $tournament, int $round)
    {
        $roundGames = $this->getTournamentHandler()->getRoundGames($tournament, $round);

        $pairs = [];
        foreach ($roundGames as $tournamentGame) {
            $pairs[] = $tournamentGame->getGame()->getUserWhite() . " " . $tournamentGame->getGame()->getUserBlack();
        }
        
        switch ($round) {
            case 1:
                $this->assertEquals([
                    'User-B User-G',
                    'User-C User-F',
                    'User-D User-E'
                ], $pairs);
                break;
            case 7:
                $this->assertEquals([
                    "User-G User-A",
                    "User-F User-B",
                    "User-E User-C"
                ], $pairs);
                break;
        }

        return $roundGames;
    }

    /**
     * @return Tournament
     */
    private function getTournament() : Tournament
    {
        $name = "Round robin test";
        try {
            return $this->getTournamentHandler()->getRepository()->findOneByName($name);
        } catch (TournamentNotFoundException $e) {
            throw $e;
        }
    }
}