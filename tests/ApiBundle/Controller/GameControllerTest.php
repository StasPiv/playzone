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
        $this->testFromJson('game/list');
    }

    public function testGame()
    {
        $this->testFromJson('game/{id}');
    }

    public function testGamePgn()
    {
        $this->testFromJson('game/{id}/pgn');
    }
}