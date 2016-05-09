<?php

namespace CoreBundle\Tests\Service\Tournament;

use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Tests\KernelAwareTest;
use CoreBundle\Entity\Tournament;
use CoreBundle\Service\Tournament\SwissService;

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

    public function testDrawEven()
    {
        $tournament = $this->getTestTournamentEven();
        $round = 1;

        $this->swissService->makeDraw($tournament, $round);
        $this->assertPlayerPlaysOnlyOneGame($tournament, $round);

        $this->assertAllPlayersTakePartInTheRound($tournament, $round);

        $this->assertNoSameOpponent($tournament, $round);
        $this->assertRequiredColors($tournament, $round);
    }

    public function testDrawOdd()
    {
        $tournament = $this->getTestTournamentOdd();
        $round = 1;

        $this->swissService->makeDraw($tournament, $round);
        $this->assertPlayerPlaysOnlyOneGame($tournament, $round);

        $this->assertOnlyOnePlayerMissedThisRound($tournament, $round);

        $this->assertNoSameOpponent($tournament, $round);
        $this->assertRequiredColors($tournament, $round);
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function assertPlayerPlaysOnlyOneGame(Tournament $tournament, int $round)
    {
        $playerIds = $this->getPlayerIdsInTournamentRound($tournament, $round);

        $this->assertEquals(
            $playerIds,
            array_unique($playerIds),
            "Some players play more than one game"
        );
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function assertAllPlayersTakePartInTheRound(Tournament $tournament, int $round)
    {
        $playerIdsInTournament = $this->getPlayerIdsInTournament($tournament);
        $playerIdsInRound = $this->getPlayerIdsInTournamentRound($tournament, $round);

        $this->assertEquals(
            $playerIdsInTournament,
            $playerIdsInRound,
            "Some players don't take part in the round"
        );
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function assertOnlyOnePlayerMissedThisRound(Tournament $tournament, int $round)
    {
        $playerIdsInTournament = $this->getPlayerIdsInTournament($tournament);
        $playerIdsInRound = $this->getPlayerIdsInTournamentRound($tournament, $round);

        $this->assertEquals(
            count($playerIdsInRound) + 1,
            count($playerIdsInTournament),
            "Only one player should miss the round"
        );
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function assertRequiredColors(Tournament $tournament, int $round)
    {
        $games = $this->getTournamentHandler()
                      ->getRoundGames($tournament, $round);

        foreach ($games as $tournamentGame) {
            $this->assertContains(
                $this->getUserRequiredColor(
                    $tournament, $tournamentGame->getGame()->getUserWhite()
                ),
                [GameColor::WHITE, GameColor::RANDOM],
                $tournamentGame->getGame()->getUserWhite() . " - " .
                $tournamentGame->getGame()->getUserBlack()
            );

            $this->assertContains(
                $this->getUserRequiredColor(
                    $tournament, $tournamentGame->getGame()->getUserBlack()
                ),
                [GameColor::BLACK, GameColor::RANDOM],
                $tournamentGame->getGame()->getUserWhite() . " - " .
                $tournamentGame->getGame()->getUserBlack()
            );
        }
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function assertNoSameOpponent(Tournament $tournament, int $round)
    {
        $games = $this->getTournamentHandler()
                      ->getRoundGames($tournament, $round);

        foreach ($games as $tournamentGame) {
            $playerWhite = $this->getTournamentHandler()->getTournamentPlayer(
                $tournament, $tournamentGame->getGame()->getUserWhite()
            );

            $playerBlack = $this->getTournamentHandler()->getTournamentPlayer(
                $tournament, $tournamentGame->getGame()->getUserBlack()
            );

            $this->assertNotContains(
                $playerBlack->getPlayer()->getId(),
                $playerWhite->getOpponents(),
                $playerWhite->getPlayer() . " has already played with {$playerBlack->getPlayer()}"
            );
            $this->assertNotContains(
                $playerWhite->getPlayer()->getId(),
                $playerBlack->getOpponents(),
                $playerBlack->getPlayer() . " has already played with {$playerWhite->getPlayer()}"
            );
        }
    }

    /**
     * @throws \Exception
     * @return Tournament
     */
    private function getTestTournamentOdd() : Tournament
    {
        return $this->getTournamentHandler()
                    ->getRepository()
                    ->findOneByName('Test switz tournament odd');
    }

    /**
     * @throws \Exception
     * @return Tournament
     */
    private function getTestTournamentEven() : Tournament
    {
        return $this->getTournamentHandler()
                    ->getRepository()
                    ->findOneByName('Test switz tournament even');
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return mixed
     * @throws \Exception
     */
    private function getPlayerIdsInTournamentRound(Tournament $tournament, int $round) : array
    {
        $games = $this->getTournamentHandler()
                      ->getRoundGames($tournament, $round);

        $playerIds =
            array_merge(
                array_map(
                    function (TournamentGame $tournamentGame) {
                        return $tournamentGame->getGame()->getUserWhite()->getId();
                    },
                    $games
                ),
                array_map(
                    function (TournamentGame $tournamentGame) {
                        return $tournamentGame->getGame()->getUserBlack()->getId();
                    },
                    $games
                )
            );

        sort($playerIds);

        return $playerIds;
    }

    /**
     * @param Tournament $tournament
     * @return mixed
     * @throws \Exception
     */
    private function getPlayerIdsInTournament(Tournament $tournament)
    {
        $playerIds = array_map(
            function(TournamentPlayer $tournamentPlayer)
            {
                return $tournamentPlayer->getPlayer()->getId();
            },
            $this->getTournamentHandler()->getPlayers($tournament)
        );

        sort($playerIds);

        return $playerIds;
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return string
     * @throws \Exception
     */
    private function getUserRequiredColor(Tournament $tournament, User $user)
    {
        return $this->getTournamentHandler()
                    ->getTournamentPlayer($tournament, $user)
                    ->getRequiredColor();
    }

    /**
     * @return \CoreBundle\Handler\TournamentHandler
     * @throws \Exception
     */
    private function getTournamentHandler() : TournamentHandler
    {
        return $this->container->get("core.handler.tournament");
    }
}