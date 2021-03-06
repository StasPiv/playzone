<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:20
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameMove;
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Game\GameStatus;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserStatService
 * @package CoreBundle\Service
 */
class UserStatService implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @return void
     */
    public function run()
    {
        $this->container->get("core.service.chess")->createPgnDir();
        
        $users = $this->container->get("doctrine")->getRepository("CoreBundle:User")->findAll();

        foreach ($users as $user) {
            $win = $draw = $lose = $rateGamesCount = 0;
            $games = array_reverse($this->container->get("core.handler.game")
                          ->getGamesForUser($user, GameStatus::END));

            foreach ($games as $game) {
                /** @var Game $game */
                if (strlen($game->getPgn()) < 0) {
                    continue;
                }

                $this->updateRateTotals($game, $user, $rateGamesCount);
                $this->updateWinDrawLose($game, $user, $win, $draw, $lose);
                $this->getPgnService()->appendUserGameToHisPgn($user, $game);
                $this->changeUserLastMove($user, $game);
            }

            $user->setWin($win)->setDraw($draw)->setLose($lose);

            $this->container->get("doctrine")->getManager()->persist($user);
        }

        $this->container->get("doctrine")->getManager()->flush();
    }

    /**
     * @return Chess\PgnService|object
     */
    private function getPgnService()
    {
        return $this->container->get('core.service.chess.pgn');
    }

    /**
     * @param Game $game
     * @param User $user
     * @param int $win
     * @param int $draw
     * @param int $lose
     */
    private function updateWinDrawLose(Game $game, User $user, int &$win, int &$draw, int &$lose)
    {
        if ($game->getUserWhite() == $user) {
            switch ($game->getResultWhite()) {
                case 1:
                    $win++;
                    break;
                case 0.5:
                    $draw++;
                    break;
                case 0:
                    $lose++;
                    break;
            }
        } elseif ($game->getUserBlack() == $user) {
            switch ($game->getResultBlack()) {
                case 1:
                    $win++;
                    break;
                case 0.5:
                    $draw++;
                    break;
                case 0:
                    $lose++;
                    break;
            }
        }
    }

    /**
     * @param User $user
     * @param Game $game
     */
    private function changeUserLastMove(User $user, Game $game)
    {
        if ($user->getLastMove() < $game->getTimeLastMove()) {
            $user->setLastMove($game->getTimeLastMove());
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GameEvents::CHANGE_STATUS_BEFORE => [
                ['onGameChangeStatus', 30]
            ]
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function onGameChangeStatus(GameEvent $event)
    {
        $game = $event->getGame();

        if ($game->getStatus() != GameStatus::END) {
            return;
        }

        $userWhite = $game->getUserWhite();
        $userBlack = $game->getUserBlack();

        $this->updateWinDrawLoseForOneGame($userWhite, $game);
        $this->updateWinDrawLoseForOneGame($userBlack, $game);

        $this->updateRateTotals($game, $userWhite);
        $this->updateRateTotals($game, $userBlack);

        $this->changeUserLastMove($userWhite, $game);
        $this->changeUserLastMove($userBlack, $game);
    }

    /**
     * @param Game $game
     * @param User $user
     * @param $rateGamesCount
     */
    private function updateRateTotals(Game $game, User $user, &$rateGamesCount = null)
    {
        if (!$game->isRate()) {
            return;
        }

        $user->setRateGamesCount(
            is_null($rateGamesCount) ? $user->getRateGamesCount() + 1 : ++$rateGamesCount
        );
    }

    /**
     * @param User $user
     * @param Game $game
     */
    private function updateWinDrawLoseForOneGame(User $user, Game $game)
    {
        $win = $user->getWin();
        $draw = $user->getDraw();
        $lose = $user->getLose();

        $this->updateWinDrawLose($game, $user, $win, $draw, $lose);

        $user->setWin($win)->setDraw($draw)->setLose($lose);
    }

    /**
     * @param User $user
     * @param Game|null $game
     * @return array
     */
    public function analyzeGameMove(User $user, Game $game = null): array
    {
        $criteria = [
            'user' => $user
        ];

        if ($game) {
            $criteria['game'] = $game;
        }

        /** @var GameMove[] $gameMoves */
        $gameMoves = $this->container->get('doctrine')
                     ->getRepository('CoreBundle:GameMove')
                     ->findBy($criteria, ['id' => Criteria::ASC]);

        $sumDelay = 0;
        $countGames = count($gameMoves);

        foreach ($gameMoves as $gameMove) {
            $sumDelay += $gameMove->getDelay();
        }

        $averageDelay = $sumDelay / $countGames;

        $moreThan150 = $moreThan200 = $moreThan300 = 0;

        foreach ($gameMoves as $gameMove) {
            $delayWeight = $gameMove->getDelay() / $averageDelay;
            if ($delayWeight > 1.5) {
                $moreThan150++;
            }

            if ($delayWeight > 2) {
                $moreThan200++;
            }

            if ($delayWeight > 3) {
                $moreThan300++;
            }
        }

        return [
            '>150' => $moreThan150,
            '>200' => $moreThan200,
            '>300' => $moreThan300,
            'count' => $countGames
        ];
    }
}