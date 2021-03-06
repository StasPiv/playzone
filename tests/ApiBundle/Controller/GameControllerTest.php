<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:34
 */

namespace ApiBundle\Tests\Controller;


class GameControllerTest extends BaseControllerTest
{
    public function testList()
    {
        $this->assertFromJson('game/list');
    }

    public function testGame()
    {
        $this->assertFromJson('game/{id}');
    }

    public function testGamePgn()
    {
        $this->assertFromJson('game/{id}/pgn');
    }

    public function testGameNewRobot()
    {
        $this->assertFromJson('game/newrobot');
    }

    public function testGameResign()
    {
        $this->assertFromJson('game/{id}/resign');
    }

    public function testGameOfferdraw()
    {
        $this->assertFromJson('game/{id}/offerdraw');
    }

    public function testGameAcceptdraw()
    {
        $this->assertFromJson('game/{id}/acceptdraw');
    }

    public function testAddMessage()
    {
        $this->assertFromJson('game/{id}/addmessage');
    }

    public function testPublishGame()
    {
        $this->assertFromJson('game/{id}/publish');
    }

    public function testFixResult()
    {
        $this->assertFromJson('game/{id}/fix');
    }

    public function testAbortGame()
    {
        $this->assertFromJson('game/{id}/abort');
    }
}