<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.04.16
 * Time: 19:54
 */

namespace CoreBundle\Tests\Service;

use CoreBundle\Service\ChessService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ChessServiceTest
 * @package CoreBundle\Tests\Service
 */
class ChessServiceTest extends KernelTestCase
{
    /**
     * @var ChessService
     */
    private $service;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get("core.service.chess");
    }

    public function testGetBestMoveInInitialPosition()
    {
        $move = $this->service->getBestMoveFromFen(
            "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1",
            300000,
            300000
        );
        
        $this->assertEquals("e2e4", $move);
    }
}