<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:20
 */

namespace CoreBundle\Service\Tournament;

use CoreBundle\Handler\TournamentHandler;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TournamentHandlerAwareTrait
 * @package CoreBundle\Service\Tournament
 */
trait TournamentHandlerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return TournamentHandler
     * @throws \Exception
     */
    protected function getTournamentHandler()
    {
        return $this->container->get("core.handler.tournament");
    }
}