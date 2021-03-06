<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.05.16
 * Time: 18:07
 */

namespace CoreBundle\Service\Tournament;


use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Exception\Handler\Tournament\TournamentGameShouldBeSkippedException;
use CoreBundle\Exception\Handler\Tournament\TournamentRoundAlreadyExistsException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Tournament\TournamentCalculatorInterface;
use CoreBundle\Model\Tournament\TournamentDrawInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class RoundrobinService
 * @package CoreBundle\Service\Tournament
 */
class RoundrobinService implements TournamentDrawInterface, TournamentCalculatorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * RoundrobinService constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     * @return void
     */
    public function makeDraw(Tournament $tournament, int $round)
    {
        if (count($this->getTournamentHandler()->getRoundGames($tournament, $round))) {
            $this->getTournamentHandler()->clearRound($tournament, $round);
        }

        $this->createGamesAsForFirstLap(
            $tournament, $round
        );

        $this->manager->flush();
    }

    /**
     * @param Tournament $tournament
     * @return void
     */
    public function makeDrawForNextRound(Tournament $tournament)
    {
        $nextRound = $tournament->getCurrentRound() + 1;
        $this->makeDraw($tournament, $nextRound);
        $tournament->setCurrentRound($nextRound);
        $this->manager->flush($tournament);
    }

    /**
     * @return TournamentHandler
     */
    private function getTournamentHandler()
    {
        return $this->container->get("core.handler.tournament");
    }

    /**
     * @param Tournament $tournament
     * @return array|TournamentPlayer[]
     */
    private function getPlayerNumbersMap(Tournament $tournament)
    {
        $i = 0;
        $sortedPlayers = [];

        foreach ($this->getTournamentHandler()->getPlayers($tournament) as $tournamentPlayer) {
            // we don't need for required color for round robin
            $tournamentPlayer->setRequiredColor(GameColor::RANDOM);
            $sortedPlayers[++$i] = $tournamentPlayer;
        }
        
        uasort(
            $sortedPlayers,
            function(TournamentPlayer $tournamentPlayerA, TournamentPlayer $tournamentPlayerB)
            {
                return $tournamentPlayerA->getPlayer()->getLogin() <=> $tournamentPlayerB->getPlayer()->getLogin();                
            }
        );

        $countPlayers = count($sortedPlayers);

        if ($countPlayers % 2 != 0) {
            $tournamentPlayer = (new TournamentPlayer())->setId(0);
            $sortedPlayers[++$i] = $tournamentPlayer;
        }

        return $sortedPlayers;
    }

    /**
     * @param int $numberFirst
     * @param int $numberSecond
     * @param int $countPlayers
     * @return int
     */
    private function getRoundForPair(int $numberFirst, int $numberSecond, int $countPlayers) : int
    {
        switch (true) {
            case $numberFirst == $countPlayers:
                return $this->getRoundForLast($numberSecond, $countPlayers);
            case $numberSecond == $countPlayers:
                return $this->getRoundForLast($numberFirst, $countPlayers);
            default:
                return $this->getRoundForPairWithoutLast($numberFirst, $numberSecond, $countPlayers);
        }
    }

    /**
     * @param int $numberFirst
     * @param int $numberSecond
     * @param int $countPlayers
     * @return int
     */
    private function getRoundForPairWithoutLast(int $numberFirst, int $numberSecond, int $countPlayers) : int
    {
        $sum = $numberFirst + $numberSecond;

        if ($sum > $countPlayers) {
            return $sum - $countPlayers;
        } else {
            return $sum - 1;
        }
    }

    /**
     * @param int $numberOpponent
     * @param int $countPlayers
     * @return int
     */
    private function getRoundForLast(int $numberOpponent, int $countPlayers) : int
    {
        $multiple = $numberOpponent * 2;

        if ($multiple > $countPlayers) {
            return $multiple - $countPlayers;
        } else {
            return $multiple - 1;
        }
    }

    /**
     * @param int $numberFirst
     * @param int $numberSecond
     * @param int $countPlayers
     * @return bool
     */
    private function checkWhiteColor(int $numberFirst, int $numberSecond, int $countPlayers) : bool
    {
        switch (true) {
            case $numberFirst == $countPlayers:
                return $this->checkWhiteColorForLast($numberSecond, $countPlayers);
            case $numberSecond == $countPlayers:
                return $this->checkWhiteColorForLast($numberFirst, $countPlayers);
            default:
                return $this->checkWhiteColorWithoutLast($numberFirst, $numberSecond);
        }
    }

    /**
     * @param int $numberOpponent
     * @param int $countPlayers
     * @return bool
     */
    private function checkWhiteColorForLast(int $numberOpponent, int $countPlayers) : bool
    {
        return ($countPlayers / 2) >= $numberOpponent;
    }

    /**
     * @param int $numberWhite
     * @param int $numberBlack
     * @return bool
     */
    private function checkWhiteColorWithoutLast(int $numberWhite, int $numberBlack) : bool
    {
        return ($numberWhite + $numberBlack) % 2 != 0 && $numberWhite < $numberBlack;
    }

    /**
     * @param Tournament $tournament
     * @param int $round
     */
    private function createGamesAsForFirstLap(Tournament $tournament, int $round)
    {
        $sortedPlayers = $this->getPlayerNumbersMap($tournament);
        $countSortedPlayers = count($sortedPlayers);
        $gamesInLap = $countSortedPlayers - 1;
        $currentLap = ceil($round / $gamesInLap);

        $roundInFirstLap = $round - ($currentLap - 1) * $gamesInLap;


        foreach ($sortedPlayers as $numberFirst => $tournamentPlayerFirst) {
            foreach ($sortedPlayers as $numberSecond => $tournamentPlayerOpponent) {
                if ($numberFirst >= $numberSecond) {
                    continue;
                }

                if ($roundInFirstLap == $this->getRoundForPair(
                        $numberFirst, $numberSecond, $countSortedPlayers
                    )
                ) {
                    $firstPlayerPlayWhite =
                        $this->checkWhiteColor($numberFirst, $numberSecond, $countSortedPlayers);
                    
                    if ($currentLap % 2 == 0) {
                        $firstPlayerPlayWhite = !$firstPlayerPlayWhite;
                    }

                    if ($firstPlayerPlayWhite) {
                        $tournamentPlayerFirst->setRequiredColor(GameColor::WHITE);
                        $tournamentPlayerOpponent->setRequiredColor(GameColor::BLACK);
                    } else {
                        $tournamentPlayerFirst->setRequiredColor(GameColor::BLACK);
                        $tournamentPlayerOpponent->setRequiredColor(GameColor::WHITE);
                    }

                    try {
                        $this->manager->persist(
                            $this->getTournamentHandler()
                                ->createTournamentGame(
                                    $tournament,
                                    $round,
                                    $tournamentPlayerFirst,
                                    $tournamentPlayerOpponent
                                )
                        );
                    } catch (TournamentGameShouldBeSkippedException $e) {

                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function calculate(Tournament $tournament)
    {
        foreach ($tournament->getPlayers() as $tournamentPlayer) {
            $tournamentPlayer->setCoefficient(0)->setPoints(0);
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            $this->updatePoints(
                $tournamentGame->getPlayerWhite(),
                $tournamentGame->getGame()->getResultWhite()
            );

            $this->updatePoints(
                $tournamentGame->getPlayerBlack(),
                $tournamentGame->getGame()->getResultBlack()
            );
        }

        foreach ($tournament->getGames() as $tournamentGame) {
            $this->updateCoefficients(
                $tournamentGame->getPlayerWhite(),
                $tournamentGame->getPlayerBlack(),
                $tournamentGame->getGame()->getResultWhite()
            );

            $this->updateCoefficients(
                $tournamentGame->getPlayerBlack(),
                $tournamentGame->getPlayerWhite(),
                $tournamentGame->getGame()->getResultBlack()
            );

            $this->manager->persist($tournamentGame->getPlayerWhite());
            $this->manager->persist($tournamentGame->getPlayerBlack());
        }

        $this->manager->flush();
    }

    /**
     * @param TournamentPlayer $player
     * @param float $result
     */
    private function updatePoints(
        TournamentPlayer $player,
        float $result
    ) {
        $player->setPoints(
            $player->getPoints() + $result
        );
    }

    /**
     * @inheritDoc
     */
    public function updateCoefficients(
        TournamentPlayer $player,
        TournamentPlayer $opponent,
        float $result
    ) {
        $player->setCoefficient(
            $player->getCoefficient() + $result * $opponent->getPoints()
        );
    }
}