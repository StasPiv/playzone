<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.06.16
 * Time: 22:05
 */

namespace ImmortachessNetBundle\Tests\Service;

use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use ImmortalchessNetBundle\Service\ImmortalchessnetService;

/**
 * Class ImmortachessNetBundleTest
 * @package ImmortachessNetBundle\Tests\Service
 */
class ImmortachessNetBundleTest extends KernelTestCase
{
    /**
     * @var ImmortalchessnetService
     */
    private $service;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get("immortalchessnet.service.immortalchessnet");
    }

    public function testOnNewTournament()
    {
        $event = new TournamentScheduler();
        $event->setTournamentId(2151);

        $this->service->onTournamentNew($event);
    }
}