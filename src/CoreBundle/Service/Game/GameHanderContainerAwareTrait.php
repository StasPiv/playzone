<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:09
 */

namespace CoreBundle\Service\Game;

use CoreBundle\Handler\GameHandler;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class UserHanderContainerAwareTrait
 * @package CoreBundle\Service
 */
trait GameHanderContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return GameHandler
     * @throws \Exception
     */
    protected function getGameHandler()
    {
        return $this->container->get("core.handler.game");
    }
}