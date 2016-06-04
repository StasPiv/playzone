<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 22:57
 */

namespace CoreBundle\Tests\Handler\User;

use CoreBundle\Entity\User;
use CoreBundle\Service\User\UserHanderContainerAwareTrait;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class UserMarkOfflineTest
 * @package CoreBundle\Tests\Handler
 */
class UserMarkOfflineTest extends KernelAwareTest
{
    use UserHanderContainerAwareTrait;

    public function testMarkUsersOffline()
    {
        $this->getManager()->persist(
            $this->getUserThatShouldBeOnline()->setOnline(true)
        );

        $this->getManager()->persist(
            $this->getUserThatShouldBeOffline()->setOnline(true)
        );

        $this->getManager()->flush();

        $this->getUserHandler()->markUsersOfflineWhoJustGone();

        $this->assertFalse($this->getUserThatShouldBeOffline()->isOnline());
        $this->assertTrue($this->getUserThatShouldBeOnline()->isOnline());
    }

    /**
     * @return User
     */
    private function getUserThatShouldBeOnline()
    {
        return $this->getUserHandler()->getRepository()->findOneByLogin("Stas")->setLastPing(new \DateTime());
    }

    /**
     * @return User
     */
    private function getUserThatShouldBeOffline()
    {
        return $this->getUserHandler()->getRepository()->findOneByLogin("Petro")
            ->setLastPing(new \DateTime("-61second"));
    }
}