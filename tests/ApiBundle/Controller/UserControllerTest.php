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
        $client = static::createClient();

        $client->getContainer()->get("session")
               ->set(
                   UserService::CURRENT_USER,
                   [
                      "login" => "UnitTestLogin",
                      "email" => "unittest@yandex.ru",
                      "rating" => 2200,
                      "class" => "N"
                   ]
               );
        $this->testFromJson('user', $client);
    }

    public function testAuth()
    {
        $this->testFromJson('user/auth', static::createClient());
    }
}