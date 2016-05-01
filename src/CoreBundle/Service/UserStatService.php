<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:20
 */

namespace CoreBundle\Service;

use CoreBundle\Model\Game\GameStatus;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UserStatService
 * @package CoreBundle\Service
 */
class UserStatService
{
    use ContainerAwareTrait;
    
    public function run()
    {
        $users = $this->container->get("doctrine")->getRepository("CoreBundle:User")->findAll();
        
        $win = $draw = $lose = 0;
        
        foreach ($users as $user) {
            $games = $this->container->get("core.handler.game")->getGamesForUser($user, GameStatus::END);
            
            foreach ($games as $game) {
                if (strlen($game->getPgn() < 20)) {
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
            }
            
            $user->setWin($win)->setDraw($draw)->setLose($lose);
            
            $this->container->get("doctrine")->getManager()->persist($user);
        }

        $this->container->get("doctrine")->getManager()->flush();
    }
}