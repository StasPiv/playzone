<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:35
 */

namespace CoreBundle\DataFixtures\ORM;


use CoreBundle\Entity\ChatMessage;
use CoreBundle\Entity\User;
use CoreBundle\Model\ChatMessage\ChatMessageType;

/**
 * Class ChatFixtures
 * @package CoreBundle\DataFixtures\ORM
 */
class ChatFixtures extends AbstractPlayzoneFixtures
{

    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $chatMessage = new ChatMessage();

        /** @var User $user */
        $user = $this->getReference($data["user"]);
        
        $chatMessage->setMessage($data["message"])
                    ->setType(new ChatMessageType($data["type"]))
                    ->setTime(new \DateTime($data["time"]))
                    ->setUser($user);
        
        return $chatMessage;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 120;
    }
}