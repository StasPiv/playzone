<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.01.17
 * Time: 22:40
 */

namespace CoreBundle\Tests\Service\Tournament;

use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use CoreBundle\Model\Tournament\TournamentType;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class TournamentStartTest
 * @package CoreBundle\Tests\Service\Tournament
 */
class TournamentStartTest extends KernelAwareTest
{
    /**
     * @var TournamentHandler
     */
    protected $handler;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->handler = $this->container->get("core.handler.tournament");
    }

    public function testOnePlayerOnline()
    {
        $name = 'Tournament one player online';
        $tournament = $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);

        $this->container->get('event_dispatcher')
            ->dispatch(TournamentEvents::START, (new TournamentContainer())->setTournament($tournament));

        $this->expectException(TournamentNotFoundException::class);
        $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);
    }

    public function testFourPlayersOnlineButOneOffline()
    {
        $name = 'Tournament four players online, but one offline';
        $tournament = $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);

        self::assertEquals(5, $tournament->getPlayers()->count());

        $this->container->get('event_dispatcher')
            ->dispatch(TournamentEvents::START, (new TournamentContainer())->setTournament($tournament));

        $tournament = $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);

        self::assertEquals(6, $tournament->getRounds());
        self::assertEquals(4, $tournament->getPlayers()->count());
    }

    public function testNinePlayersOnline()
    {
        $name = 'Tournament nine players online';
        $tournament = $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);

        self::assertEquals(9, $tournament->getPlayers()->count());

        $this->container->get('event_dispatcher')
            ->dispatch(TournamentEvents::START, (new TournamentContainer())->setTournament($tournament));

        $tournament = $this->getManager()->getRepository('CoreBundle:Tournament')
            ->findOneByName($name);

        self::assertEquals(TournamentType::SWITZ, $tournament->getTournamentParams()->getType());
        self::assertEquals(6, $tournament->getRounds());
        self::assertEquals(9, $tournament->getPlayers()->count());
    }
}