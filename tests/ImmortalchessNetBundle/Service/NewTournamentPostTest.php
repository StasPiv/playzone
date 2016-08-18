<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.08.16
 * Time: 20:34
 */

namespace ImmortachessNetBundle\Tests\Service;

use CoreBundle\Entity\Tournament;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class NewTournamentPostTest
 * @package ImmortachessNetBundle\Tests\Service
 */
class NewTournamentPostTest extends KernelAwareTest
{

    public function testNewTournamentMessage()
    {
        /** @var Tournament $tournament */
        $tournament = $this->container->get('core.handler.tournament')->getRepository()->findBy([])[0];

        $tournament->getTournamentParams()->setTimeBegin(new \DateTime('+90minute'));

        $html = $this->container->get('twig')->render(':Post:newtournament.html.twig', [
            'tournament' => $tournament
        ]);

        self::assertContains('через 1 час 30 минут', $html);
    }

    public function testNew()
    {
        $datetime1 = new \DateTime('now');
        $datetime2 = new \DateTime('+20minute');
        $interval = $datetime1->diff($datetime2);
        echo $interval->format('%R%a days');
    }
}