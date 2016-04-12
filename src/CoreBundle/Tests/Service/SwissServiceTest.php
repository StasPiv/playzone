<?php

namespace CoreBundle\Tests\Service;

use CoreBundle\Entity\TournamentGame;
use CoreBundle\Tests\KernelAwareTest;
use CoreBundle\Entity\Tournament;
use CoreBundle\Service\SwissService;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.04.16
 * Time: 22:16
 */
class SwissServiceTest extends KernelAwareTest
{
    /**
     * @var SwissService
     */
    protected $swissService;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->swissService = $this->container->get("core.service.swiss");
    }

    public function testDrawFirstRound()
    {
        $tournament = $this->getTournament();
        $round = 1;

        $this->swissService->makeDraw($tournament, $round);

        $tournamentMap = [
            [
                "round" => $round,
                "whiteLogin" => "User-A",
                "blackLogin" => "User-N"
            ],
            [
                "round" => $round,
                "whiteLogin" => "User-B",
                "blackLogin" => "User-O"
            ]
        ];

        foreach ($tournamentMap as $tournamentGameArray) {
            $this->assertTrue(
                $this->isTournamentGameExists(
                    $tournament,
                    $tournamentGameArray["round"],
                    $tournamentGameArray["whiteLogin"],
                    $tournamentGameArray["blackLogin"]
                )
            );
        }
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @param string $whiteLogin
     * @param string $blackLogin
     * @return bool
     * @throws \Exception
     */
    private function isTournamentGameExists(Tournament $tournament, int $round, string $whiteLogin, string $blackLogin) : bool
    {
        return count(
            array_filter(
                $this->container->get("doctrine")->getRepository("CoreBundle:TournamentGame")
                    ->findBy(
                        [
                            "tournament" => $tournament,
                            "round" => $round
                        ]
                    ),
                function(TournamentGame $tournamentGame) use ($whiteLogin, $blackLogin)
                {
                    return $tournamentGame->getGame()->getUserWhite()->getLogin() === $whiteLogin &&
                           $tournamentGame->getGame()->getUserBlack()->getLogin() === $blackLogin;
                }
            )
        ) === 1;
    }

    /**
     * @throws \Exception
     * @return Tournament
     */
    private function getTournament() : Tournament
    {
        $tournament = $this->container->get("core.handler.tournament")->getRepository()
                                      ->findOneBy(
                                          [
                                              'name' => 'Test switz tournament'
                                          ]
                                      );
        return $tournament;
    }
}