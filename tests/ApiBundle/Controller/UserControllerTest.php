<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:44
 */

namespace ApiBundle\Tests\Controller;

use CoreBundle\Service\UserService;

class UserControllerTest extends BaseControllerTest
{
    public function testRegister()
    {
        $this->testFromJson('user/register', static::createClient());
    }

    public function testGet()
    {
        $this->testFromJson('user', static::createClient());
    }

    public function testAuth()
    {
        $this->testFromJson('user/auth', static::createClient());
    }
}