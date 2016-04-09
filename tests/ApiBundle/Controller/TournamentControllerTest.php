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

    public function testTournamentRecord()
    {
        $this->assertFromJson('tournament/{tournament_id}/record');
    }

    public function testTournamentUnrecord()
    {
        $this->assertFromJson('tournament/{tournament_id}/unrecord');
    }

    public function testTournament()
    {
        $this->assertFromJson('tournament/{tournament_id}');
    }


}