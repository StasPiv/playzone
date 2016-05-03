<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.01.16
 * Time: 23:35
 */

namespace CoreBundle\Tests\Handler;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class UserHandlerTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    public function testGetUserByTokenCorrect()
    {
        $this->assertInstanceOf(
            'CoreBundle\Entity\User',
            $this->container->get("core.handler.user")->getUserByLoginAndToken(
                "TestLogin", "aba37b62d15cc5f8671fd3d1b034c354"
            )
        );
    }

    public function testGetUserByTokenNull()
    {
        $this->expectException('CoreBundle\Exception\Handler\User\TokenNotCorrectException');
        $this->container->get("core.handler.user")->getUserByLoginAndToken(
            "TestLogin", "sba37b62d15cc5f8671fd3d1b034c354"
        );
    }
}