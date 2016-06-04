<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:20
 */

namespace CoreBundle\Tests\Handler;

use CoreBundle\Service\Tournament\TournamentHandlerAwareTrait;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class TournamentHandlerTest
 * @package CoreBundle\Tests\Handler
 */
class TournamentHandlerTest extends KernelAwareTest
{
    use TournamentHandlerAwareTrait;

    public function testRemoveOfflinePlayers()
    {
        $tournament = $this->getTournamentHandler()->getRepository()->findOneByName("Round robin test");
        $this->assertNotEmpty($tournament->getPlayers());

        $players = $tournament->getPlayers();

        foreach ($players as $tournamentPlayer) {
            $this->getManager()->persist($tournamentPlayer->getPlayer()->setOnline(false));
        }

        $this->getManager()->flush();

        $this->getTournamentHandler()->removeOfflinePlayers($tournament);

        $this->assertEmpty($tournament->getPlayers());
    }
}