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
        $this->assertFromJson('call');
    }

    public function testCallSend()
    {
        $this->assertFromJson('call/send');
    }

    public function testCallRemove()
    {
        $this->assertFromJson('call/{call_id}/remove');
    }

    public function testCallAccept()
    {
        $this->assertFromJson('call/{call_id}/accept');
    }

    public function testCallDecline()
    {
        $this->assertFromJson('call/{call_id}/decline');
    }
}