<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:00
 */

namespace ApiBundle\Tests\Controller;


class CallControllerTest extends BaseControllerTest
{

    public function testCallSend()
    {
        $this->testFromJson('call/send');
    }

    public function testCallRemove()
    {
        $this->testFromJson('call/remove');
    }

    public function testCallAccept()
    {
        $this->testFromJson('call/accept');
    }
}