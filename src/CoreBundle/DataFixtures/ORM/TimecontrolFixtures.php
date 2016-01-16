<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 23:04
 */

namespace CoreBundle\DataFixtures\ORM;


use CoreBundle\Entity\Timecontrol;

class TimecontrolFixtures extends AbstractPlayzoneFixtures
{
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $timecontrol = new Timecontrol();

        $timecontrol->setId($data['id'])
            ->setName($data['name'])
            ->setBegin($data['begin'])
            ->setIncrement($data['increment'])
            ->setMoves($data['moves'])
            ->setTimeLimit($data['timeLimit'])
            ->setRest($data['rest']);

        return $timecontrol;
    }

}