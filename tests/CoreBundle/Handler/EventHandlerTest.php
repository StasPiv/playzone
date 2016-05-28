<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 20:00
 */

namespace CoreBundle\Tests\Handler;

use CoreBundle\Model\Event\Tournament\TournamentInitializator;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentType;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class EventHandlerTest
 * @package CoreBundle\Tests\Handler
 */
class EventHandlerTest extends KernelAwareTest
{
    public function testSaveEvent()
    {
        $this->getEventHandler()->initEventAndSave(
            $this->createTournamentEvent(), "core.service.event.tournament.create"
        );
    }

    /**
     * @return \CoreBundle\Handler\EventHandler
     * @throws \Exception
     */
    private function getEventHandler()
    {
        return $this->container->get("core.handler.event");
    }

    /**
     * @return TournamentInitializator
     */
    private function createTournamentEvent() : TournamentInitializator
    {
        $frequency = '25 20 * * * *';
        $timeBegin = "+1minute";
        $tournamentName = "Create new tournament";
        $timeBase = 180000;

        return (new TournamentInitializator())
                    ->setFrequency($frequency)
                    ->setTournamentName($tournamentName)
                    ->setGameParams(
                        (new GameParams())->setTimeBase($timeBase)
                    )
                    ->setTimeBegin($timeBegin)
                    ->setTournamentParams(
                        TournamentParamsFactory::create(TournamentType::ROUND_ROBIN())
                    );
    }
}