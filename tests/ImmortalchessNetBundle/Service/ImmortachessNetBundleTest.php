<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.06.16
 * Time: 22:05
 */

namespace ImmortachessNetBundle\Tests\Service;

use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentScheduler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use ImmortalchessNetBundle\Service\ImmortalchessnetService;
use Symfony\Component\DependencyInjection\Container;

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

    /**
     * @var Container
     */
    protected $container;

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

    public function testOnTournamentFinish()
    {
        $event = new TournamentContainer();
        $tournament = $this->container->get("core.handler.tournament")->getRepository()->find(2163);

        $event->setTournament($tournament);

        $this->service->onTournamentFinish($event);
    }
}