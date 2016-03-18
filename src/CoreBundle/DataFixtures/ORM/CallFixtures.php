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
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameParams;

class CallFixtures extends AbstractPlayzoneFixtures
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

        if (isset($data["game_params"])) {
            $gameParams = new GameParams();
            $gameParams->setColor(new GameColor($data["game_params"]["color"]));
            $gameCall->setGameParams($gameParams);
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