<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:57
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;

class GameCallFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $gameCall = new GameCall();

        /** @var User $fromUser */
        $fromUser = $this->getReference($data['fromUser']);

        /** @var User $toUser */
        $toUser = $this->getReference($data['toUser']);

        $gameCall->setFromUser($fromUser)
                 ->setToUser($toUser);

        if (!empty($data['game'])) {
            /** @var Game $game */
            $game = $this->getReference($data['game']);
            $gameCall->setGame($game);
        }

        return $gameCall;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
    }

}