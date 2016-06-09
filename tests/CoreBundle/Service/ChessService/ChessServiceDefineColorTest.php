<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 16:18
 */

namespace CoreBundle\Tests\Service;

use CoreBundle\Tests\KernelAwareTest;

/**
 * Class ChessServiceDefineColorTest
 * @package CoreBundle\Tests\Service
 */
class ChessServiceDefineColorTest extends KernelAwareTest
{
    
    private $testCases = [
        [
            "pgn" => "1. e4 c5 2. Nc3 Nc6 3. Nf3 g6 4. Bc4 d6 5. O-O e5 6. d3 Bg7 7. Ng5 Nh6 8. Bd2 O-O 9. f3 Be6 10. Bxe6 fxe6 11. Nxe6 Qd7 12. Nxf8 Rxf8 13. Nd5 Ne7 14. Nxe7+ Qxe7 15. c3 Nf7 16. Qb3 b6 17. a4 Kh8 18. a5 b5 19. Qxb5 Nd8 20. a6 Ne6 21. Qb7 Qxb7 22. axb7 Rb8 23. Rxa7 Nd8 24. Ra8 Rxb7 25. Rxd8+ Bf8 26. Bh6 Rf7 27. Bxf8 h5 28. Bxd6+ Kh7 29. Bxe5 Rb7 30. Rh8#",
            "color" => "b"
        ],
        [
            "pgn" => "1. f4 d5 2. g4 e5 3. b4 Qh4#",
            "color" => "w"
        ]
    ];

    public function testDefineColor()
    {
        foreach ($this->testCases as $case) {
            $actualColor = $this->container->get("core.service.chess")->defineColorToMoveByPgn($case["pgn"]);

            $this->assertEquals($case["color"], $actualColor);
        }
    }
}