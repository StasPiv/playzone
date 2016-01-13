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
        $this->testFromJson('user/register');
    }

    public function testAuth()
    {
        $this->testFromJson('user/auth');
    }

    public function testList()
    {
        $this->testFromJson('user/list', ['id', 'token']);
    }
}