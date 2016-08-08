<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:05
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Problem;

/**
 * Class ProblemFixtures
 * @package CoreBundle\DataFixtures\ORM
 */
class ProblemFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @inheritDoc
     */
    protected function createEntity($data)
    {
        $problem = new Problem();

        $problem->setPgn($data["pgn"])->setFen($data["fen"]);

        return $problem;
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 200;
    }

}