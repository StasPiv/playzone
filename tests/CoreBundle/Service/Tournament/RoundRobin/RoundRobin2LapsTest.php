<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.07.16
 * Time: 20:48
 */

namespace CoreBundle\Tests\Service\Tournament\RoundRobin;

use CoreBundle\Entity\Tournament;
use CoreBundle\Service\Tournament\RoundrobinService;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class RoundRobin2LapsTest
 * @package CoreBundle\Tests\Service\Tournament\RoundRobin
 */
class RoundRobin2LapsTest extends KernelAwareTest
{
    use RoundRobinTestTrait;

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
        $tournament = $this->getTournament("Round robin test 2 laps");

        for ($round = 1; $round <= 6; $round++) {
            $this->service->makeDraw($tournament, $round);

            $this->assertGames($tournament, $round);
        }

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
                    'User-B User-C'
                ], $pairs);
                break;
            case 2:
                $this->assertEquals([
                    'User-A User-B'
                ], $pairs);
                break;
            case 3:
                $this->assertEquals([
                    'User-C User-A'
                ], $pairs);
                break;
            case 4:
                $this->assertEquals([
                    'User-C User-B'
                ], $pairs);
                break;
            case 5:
                $this->assertEquals([
                    'User-B User-A'
                ], $pairs);
                break;
            case 6:
                $this->assertEquals([
                    'User-A User-C'
                ], $pairs);
                break;
        }

        return $roundGames;
    }
}