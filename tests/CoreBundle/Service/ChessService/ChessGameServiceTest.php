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

    public function testFixCheckmate()
    {
        $pgn = "1. d4 d5 2. c4 c6 3. Nc3 Nf6 4. Bg5 dxc4 5. Bxf6 exf6 6. e4 b5 7. a4 Bb4 8. axb5 cxb5 9. Nf3 O-O 10. Be2 Bb7 11. d5 Re8 12. Nd2 Bxc3 13. bxc3 f5 14. f3 fxe4 15. fxe4 Qh4+ 16. g3 Qh3 17. Bf3 Bxd5 18. Qe2 Nd7 19. Qg2 Qh6 20. O-O Bc6 21. Ra6 Ne5 22. Re1 Qxd2 23. Qxd2 Nxf3+ 24. Kf2 Nxd2 25. Rxc6 Nxe4+ 26. Kg2 Nxc3 27. Ra1 a5 28. Rb6 a4 29. Rf1 a3 30. Rf3 a2 31. Rf1 Nb1 32. Rxb5 a1=Q 33. Rbxb1 Ra2+ 34. Kg1 Qd4+ 35. Kh1 Qe4+ 36. Kg1 Qg2#";

        $game = $this->container->get("core.service.chess.game");
        $game->setPgn($pgn);

        $this->assertEquals("B", $game->gameOver());
    }

    public function testFixDrawByRepetition()
    {
        $pgn = "1.e4 e5 2.Nf3 Nc6 3.Bb5 Nf6 4.O-O Bc5 5.c3 O-O 6.d4 Bb6 7.Bg5 d6 8.Bxc6 bxc6 9.dxe5 dxe5 10.Qa4 h6 11.Bh4 Qd3 12.Bxf6 gxf6 13.Nbd2 Be6 14.c4 Rad8 15.Qxc6 Kh7 16.Qa4 Rg8 17.Kh1 f5 18.Rae1 f4 19.Rc1 c5 20.Qb5 f6 21.Rfe1 Bd7 22.Qa6 Bc6 23.a4 Qd7 24.Ra1 Bb7 25.Qb5 Qc8 26.a5 Bc6 27.Qb3 Bc7 28.Qa3 Rg7 29.h3 Rgd7 30.Re2 Qa6 31.b3 Qc8 32.Kh2 Qb7 33.Qxc5 Bd6 34.a6 Bxc5 35.axb7 Rxb7 36.Ra6 Rd6 37.Kg1 Rc7 38.Re1 Bb4 39.Rd1 Kg6 40.Kf1 h5 41.Ke2 Rcd7 42.Kf1 Rc7 43.Ke2 Rcd7 44.Kf1 Rc7 45.Ke2 Rcd7 46.Kf1 Rc7 47.Ke2 Rcd7 48.Kf1 Rc7 49.Ke2 Rcd7 50.Kf1";

        $game = $this->container->get("core.service.chess.game");
        $game->setPgn($pgn);

        $this->assertEquals("D", $game->gameOver());
    }


}