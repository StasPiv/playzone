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

        $gameCall->setFromUser($fromUser);

        if (isset($data['toUser'])) {
            /** @var User $toUser */
            $toUser = $this->getReference($data['toUser']);
            $gameCall->setToUser($toUser);
        }

        if (isset($data["game_params"])) {
            /** @var GameParams $gameParams */
            $gameParams = $this->container->get("jms_serializer")->deserialize(
                json_encode($data["game_params"]),
                'CoreBundle\Model\Game\GameParams',
                'json'
            );
            
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