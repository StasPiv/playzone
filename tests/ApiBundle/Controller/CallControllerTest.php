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
    public function testCall()
    {
        $this->testFromJson('call');
    }

    public function testCallSend()
    {
        $this->testFromJson('call/send');
    }

    public function testCallRemove()
    {
        $this->testFromJson('call/{call_id}/remove');
    }

    public function testCallAccept()
    {
        $this->testFromJson('call/{call_id}/accept');
    }

    public function testCallDecline()
    {
        $this->testFromJson('call/{call_id}/decline');
    }
}