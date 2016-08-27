<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:20
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Game\GameStatus;
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
            $win = $draw = $lose = 0;
            $games = array_reverse($this->container->get("core.handler.game")
                          ->getGamesForUser($user, GameStatus::END));

            foreach ($games as $game) {
                /** @var Game $game */
                if (strlen($game->getPgn()) < 0) {
                    continue;
                }

                $this->countWinDrawLose($game, $user, $win, $draw, $lose);
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
    private function countWinDrawLose(Game $game, User $user, int &$win, int &$draw, int &$lose)
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
        if ($event->getGame()->getStatus() != GameStatus::END) {
            return;
        }

        $this->changeUserLastMove($event->getGame()->getUserWhite(), $event->getGame());
        $this->changeUserLastMove($event->getGame()->getUserBlack(), $event->getGame());
    }
}