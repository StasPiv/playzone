<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 22:45
 */

namespace CoreBundle\Tests\Service;

use CoreBundle\Tests\KernelAwareTest;

/**
 * Class PgnServiceTest
 * @package CoreBundle\Tests\Service
 */
class PgnServiceTest extends KernelAwareTest
{

    public function testGetRandomPgnGame()
    {
        $pgnGame = $this->container->get("core.service.chess.pgn")->getRandomPgnGame(
            __DIR__ . DIRECTORY_SEPARATOR . 'pgn' . DIRECTORY_SEPARATOR . 'test.pgn'
        );

        $this->assertNotEmpty($pgnGame->getFen());
        $this->assertNotEmpty($pgnGame->getWhite());
        $this->assertNotEmpty($pgnGame->getMoves());
        $this->assertNotEmpty($pgnGame->getPgn());
    }
}