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

    const POSTID_FOR_GRANPRI_TABLE = 753937;
    const THREADID_FOR_GRANPRI_TABLE = 33274;
    const USERID_FOR_GRANPRI_TABLE = 87;

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
    const FIRST_TOURNAMENT = 2448;

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
            $user = $this->getUserFromMap($userId);

            if ($user->isEngine() || $user->isBanned()) {
                continue;
            }

            foreach ($weekResults as $week => $weekPoints) {
                rsort($weekPoints);
                $twoBest = array_slice($weekPoints, 0, 2);

                $otherResults = array_slice($weekPoints, 2);

                $sumForPlayer = 3 * array_sum($twoBest) + array_sum($otherResults);

                if (!isset($bestPlayers[$userId])) {
                    $bestPlayers[$userId] = [
                        'player' => $user,
                        'sum' => $sumForPlayer
                    ];
                } else {
                    $bestPlayers[$userId]['sum'] += $sumForPlayer;
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
            self::POSTID_FOR_GRANPRI_TABLE,
            new Post(
                $this->container->getParameter("app_immortalchess.forum_playzone"),
                self::THREADID_FOR_GRANPRI_TABLE,
                'PozitiFF_Chess',
                self::USERID_FOR_GRANPRI_TABLE,
                'Общий зачет гран-при',
                $this->container->get("templating")->render(
                    'Post/granpri.html.twig',
                    [
                        'players' => $this->players
                    ]
                )
            )
        );

        $this->container->get('immortalchessnet.service.publish')->editPostParsed(
            self::POSTID_FOR_GRANPRI_TABLE,
            $this->container->get("templating")->render(
                'Post/granpriparsed.html.twig',
                [
                    'players' => $this->players,
                ]
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
