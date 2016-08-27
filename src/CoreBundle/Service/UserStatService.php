<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:20
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameStatus;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UserStatService
 * @package CoreBundle\Service
 */
class UserStatService
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
                if (strlen($game->getPgn()) < 0) {
                    continue;
                }

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

                $this->getPgnService()->appendUserGameToHisPgn($user, $game);
            }

            $user->setWin($win)->setDraw($draw)->setLose($lose);

            $this->container->get("doctrine")->getManager()->persist($user);

            try {
//                $this->getPgnService()->appendUserGamesToHisPgn($user, $pgnArray);
            } catch (\Throwable $e) {
                $this->container->get("logger")->err($e->getMessage());
            }
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
}