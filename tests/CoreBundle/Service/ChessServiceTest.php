<?php
/**
 * Created by PhpStorm.
 * User: ssp
 * Date: 27.04.16
 * Time: 10:15
 */

namespace CoreBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class ChessServiceTest extends KernelTestCase
{
    /** @var Container */
    private $container;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    public function testGetBestMoveInitialPosition()
    {
        $bestMove = $this->container->get("core.service.chess")->getBestMove([]);

        echo $bestMove;
    }

    public function testGetBestMoveAfterE2E4()
    {
        $bestMove = $this->container->get("core.service.chess")->getBestMove(["e2e4", "e7e5", "d2d4"]);

        echo $bestMove;
    }
}