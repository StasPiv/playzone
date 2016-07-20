<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.07.16
 * Time: 22:19
 */

namespace CoreBundle\Tests\Service\Tournament\RoundRobin;

use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Handler\TournamentHandler;
use CoreBundle\Service\Tournament\RoundrobinService;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class RoundRobinTestTrait
 * @package CoreBundle\Tests\Service\Tournament\RoundRobin
 */
trait RoundRobinTestTrait
{
    use ContainerAwareTrait;

    /**
     * @param string $name
     * @return Tournament
     */
    protected function getTournament($name) : Tournament
    {
        try {
            return $this->getTournamentHandler()->getRepository()->findOneByName($name);
        } catch (TournamentNotFoundException $e) {
            throw $e;
        }
    }

    /**
     * @return TournamentHandler
     * @throws \Exception
     */
    protected function getTournamentHandler()
    {
        return $this->container->get("core.handler.tournament");
    }
}