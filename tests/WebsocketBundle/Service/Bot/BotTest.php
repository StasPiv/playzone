<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.06.16
 * Time: 17:25
 */

namespace WebsocketBundle\Tests\Service\Bot;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use WebsocketClientBundle\Service\Bot\Bot;

/**
 * Class BotTest
 * @package WebsocketBundle\Tests\Service\Bot
 */
class BotTest extends KernelTestCase
{
    /** @var Bot */
    private $bot1;

    /** @var Bot */
    private $bot2;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->bot1 = $this->container->get("ws.playzone.bot");
        $this->bot2 = $this->container->get("ws.playzone.bot");
    }


    public function testConnectAndWaitForMessage()
    {
        
    }
}