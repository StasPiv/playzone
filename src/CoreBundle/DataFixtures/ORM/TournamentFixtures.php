<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:43
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Tournament;

/**
 * Class TournamentFixtures
 * @package CoreBundle\DataFixtures\ORM
 */
class TournamentFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $tournament = new Tournament();

        $tournament->setName($data['name']);

        return $tournament;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

}