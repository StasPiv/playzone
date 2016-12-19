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
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use ImmortalchessNetBundle\Model\Post;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class GranPriCalculator
 * @package CoreBundle\Service
 */
class GranPriCalculator implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    private $placesMap = [];

    /**
     * @var array
     */
    private $usersMap = [];

    /**
     * @var array
     */
    private $players;

    //todo: need to implement logic for separate simple tournaments from granpri
    const FIRST_TOURNAMENT = 2128;

    /**
     * @param bool $publish
     */
    public function process(bool $publish = true)
    {
        $this->calculate();

        if ($publish) {
            $this->publish();
        }
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
            $granPriPointsMap[$player->getPlayer()->getId()][$player->getTournament()->getTournamentParams()->getTimeBegin()->format('W')][$player->getTournament()->getId()] = count($player->getTournament()->getPlayers()) - $this->getPlacesMap($player->getTournament())[$player->getId()];
        }

        $bestPlayers = [];

        foreach ($granPriPointsMap as $userId => $weekResults) {
            foreach ($weekResults as $week => $weekPoints) {
                rsort($weekPoints);
                $twoBest = array_slice($weekPoints, 0, 2);

                if (!isset($bestPlayers[$userId])) {
                    $bestPlayers[$userId] = [
                        'player' => $this->getUserFromMap($userId),
                        'sum' => array_sum($twoBest)
                    ];
                } else {
                    $bestPlayers[$userId]['sum'] += array_sum($twoBest);
                }
            }
        }

        usort(
            $bestPlayers,
            function (array $first, array $second)
            {
                return $second['sum'] <=> $first['sum'];
            }
        );

        $this->players = $bestPlayers;
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

    /**
     * @param int $userId
     * @return User
     */
    private function getUserFromMap(int $userId): User
    {
        if (isset($this->usersMap[$userId])) {
            return $this->usersMap[$userId];
        }

        return $this->usersMap[$userId] = $this->container->get('doctrine')->getRepository('CoreBundle:User')->find($userId);
    }

    /**
     *
     */
    private function publish()
    {
        $this->container->get("immortalchessnet.service.publish")->editPost(
            739159,
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                32697,
                'PozitiFF_Chess',
                87,
                'Общий зачет гран-при',
                $this->container->get("templating")->render(
                    'Post/granpri.html.twig',
                    [
                        'players' => $this->players
                    ]
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TournamentEvents::TOURNAMENT_FINISHED => [
                ['onTournamentFinish', 30]
            ]
        ];
    }

    /**
     * @param TournamentContainer $tournamentContainer
     */
    public function onTournamentFinish(TournamentContainer $tournamentContainer)
    {
        $this->process(true);
    }
}