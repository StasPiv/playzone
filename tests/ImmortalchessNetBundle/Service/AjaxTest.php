<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.06.16
 * Time: 6:29
 */

namespace ImmortachessNetBundle\Tests\Service;

use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use WebsocketClientBundle\Service\Bot\Bot;

/**
 * Class AjaxTest
 * @package ImmortachessNetBundle\Tests\Service
 */
class AjaxTest extends KernelTestCase
{

    /**
     * @var Bot
     */
    private $service;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get("ws.playzone.bot");
    }

    public function testAjax()
    {
        echo $this->container->get("router")->generate("get_game", ["id" => 217]);
    }
}