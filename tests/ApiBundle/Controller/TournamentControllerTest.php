<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:40
 */

namespace ApiBundle\Tests\Controller;

/**
 * Class TournamentControllerTest
 * @package ApiBundle\Tests\Controller
 */
class TournamentControllerTest extends BaseControllerTest
{

    public function testTournamentList()
    {
        $this->assertFromJson('tournament/list');
    }
}