<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:44
 */

namespace CoreBundle\DataFixtures\ORM;


use CoreBundle\Entity\Game;
use CoreBundle\Entity\User;

class GameFixtures extends AbstractPlayzoneFixtures
{
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $game = new Game();

        /** @var User $userWhite */
        $userWhite = $this->getReference($data['user_white']);
        /** @var User $userBlack */
        $userBlack = $this->getReference($data['user_black']);
        /** @var User $userToMove */
        $userToMove = $this->getReference($data['user_to_move']);

        $game->setUserWhite($userWhite)
            ->setUserBlack($userBlack)
            ->setUserToMove($userToMove)
            ->setPgn($data['pgn'])
            ->setPgnAlt($data['pgn_alt'])
            ->setStatus($data['status'])
            ->setRate($data['rate'])
            ->setTimeWhite($data['time_white'])
            ->setTimeBlack($data['time_black'])
            ->setTimeLastMove(new \DateTime($data['time_last_move']))
            ->setDraw(@$data['draw']);

        return $game;
    }

}