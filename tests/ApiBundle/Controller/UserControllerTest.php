<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:44
 */

namespace ApiBundle\Tests\Controller;

class UserControllerTest extends BaseControllerTest
{
    public function testRegister()
    {
        $this->assertFromJson('user/register');
    }

    public function testAuth()
    {
        $this->assertFromJson('user/auth');
    }

    public function testList()
    {
        $this->assertFromJson('user/list');
    }

    public function testProfile()
    {
        $this->assertFromJson('user/{user_id}/profile');
    }

    public function testUserUpdateSetting()
    {
        $this->assertFromJson('user/{setting_id}/setting');
    }
}