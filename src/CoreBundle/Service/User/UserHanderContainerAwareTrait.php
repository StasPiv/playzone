<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:09
 */

namespace CoreBundle\Service\User;

use CoreBundle\Handler\UserHandler;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class UserHanderContainerAwareTrait
 * @package CoreBundle\Service
 */
trait UserHanderContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return UserHandler
     * @throws \Exception
     */
    protected function getUserHandler()
    {
        return $this->container->get("core.handler.user");
    }
}