<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.12.16
 * Time: 18:40
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class GranPriCalculator
 * @package CoreBundle\Service
 */
class GranPriCalculator
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    private $placesMap = [];

    //todo: need to implement logic for separate simple tournaments from granpri
    const FIRST_TOURNAMENT = 2128;

    /**
     * @param bool $publish
     */
    public function process(bool $publish = true)
    {
        $this->calculate();
    }

    /**
     * @return void
     */
    private function calculate()
    {
        $playerRepository = $this->container->get('doctrine')->getRepository('CoreBundle:TournamentPlayer');

        $tournamentRepository = $this->container->get('doctrine')->getRepository('CoreBundle:Tournament');

        /** @var TournamentPlayer[] $players */
        $players = $playerRepository->findBy(['tournament' => $tournamentRepository->findLastTournaments(self::FIRST_TOURNAMENT)]);

        $granPriPointsMap = [];

        foreach ($players as $player) {
            $granPriPointsMap[$player->getPlayer()->getId()][$player->getTournament()->getTournamentParams()->getTimeBegin()->format('W')][$player->getTournament()->getId()] = count($player->getTournament()->getPlayers()) - $this->getPlacesMap($player->getTournament())[$player->getId()] + 1;
        }

        var_dump($granPriPointsMap);

        $bestResults = [];

        foreach ($granPriPointsMap as $playerId => $weekResults) {
            foreach ($weekResults as $week => $weekPoints) {
                rsort($weekPoints);
                $twoBest = array_slice($weekPoints, 0, 2);

                if (!isset($bestResults[$playerId])) {
                    $bestResults[$playerId] = array_sum($twoBest);
                } else {
                    $bestResults[$playerId] += array_sum($twoBest);
                }
            }
        }

        ksort($bestResults);

        var_dump($bestResults);
    }

    /**
     * @param Tournament $tournament
     * @return mixed
     */
    private function getPlacesMap(Tournament $tournament)
    {
        if (isset($this->placesMap[$tournament->getId()])) {
            return $this->placesMap[$tournament->getId()];
        }

        /** @var TournamentPlayer[] $players */
        $players = $tournament->getPlayers()->toArray();

        usort(
            $players,
            function (TournamentPlayer $playerB, TournamentPlayer $playerA)
            {
                return 1000 * $playerA->getPoints() + $playerA->getCoefficient() <=> 1000 * $playerB->getPoints() + $playerB->getCoefficient();
            }
        );

        $i = 0;
        foreach ($players as $player) {
            $this->placesMap[$tournament->getId()][$player->getId()] = $i++;
        }

        return $this->placesMap[$tournament->getId()];
    }
}