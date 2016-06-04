<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:33
 */

namespace CoreBundle\Model\Event\User;
use CoreBundle\Model\User\UserContainerAwareTrait;
use CoreBundle\Model\User\UserContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserEvent
 * @package CoreBundle\Model\Event
 */
class UserEvent extends Event implements UserContainerInterface
{
    use UserContainerAwareTrait;
}