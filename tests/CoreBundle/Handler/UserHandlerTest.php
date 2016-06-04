<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.01.16
 * Time: 23:35
 */

namespace CoreBundle\Tests\Handler;

use CoreBundle\Handler\UserHandler;
use CoreBundle\Service\User\UserHanderContainerAwareTrait;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class UserHandlerTest
 * @package CoreBundle\Tests\Handler
 */
class UserHandlerTest extends KernelAwareTest
{
    use UserHanderContainerAwareTrait;

    public function testGetUserByTokenCorrect()
    {
        $this->assertInstanceOf(
            'CoreBundle\Entity\User',
            $this->getUserHandler()->getUserByLoginAndToken(
                "TestLogin", "aba37b62d15cc5f8671fd3d1b034c354"
            )
        );
    }

    public function testGetUserByTokenNull()
    {
        $this->expectException('CoreBundle\Exception\Handler\User\TokenNotCorrectException');
        $this->getUserHandler()->getUserByLoginAndToken(
            "TestLogin", "sba37b62d15cc5f8671fd3d1b034c354"
        );
    }
}