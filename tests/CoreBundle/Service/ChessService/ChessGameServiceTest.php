<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.06.16
 * Time: 10:33
 */

namespace CoreBundle\Tests\Service;

use CoreBundle\Tests\KernelAwareTest;

/**
 * Class ChessGameServiceTest
 * @package CoreBundle\Tests\Service
 */
class ChessGameServiceTest extends KernelAwareTest
{

    public function testSetPgn()
    {
        $pgn = "1.e4 c5 2.Nf3 Nf6 3.Ng5 Nxe4 4.Qh5 Nc3 5.Qxf7#";

        $game = $this->container->get("core.service.chess.game");
        $game->setPgn($pgn);

        $this->assertContains($game->getPgn(), $pgn);
    }
}