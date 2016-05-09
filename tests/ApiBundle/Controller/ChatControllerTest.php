<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:09
 */

namespace ApiBundle\Tests\Controller;

/**
 * Class ChatControllerTest
 * @package ApiBundle\Tests\Controller
 */
class ChatControllerTest extends BaseControllerTest
{
    public function testMessage()
    {
        $this->assertFromJson('chat/message');
    }

    public function testGetLastMessage()
    {
        $this->assertFromJson('chat/messages');
    }
}