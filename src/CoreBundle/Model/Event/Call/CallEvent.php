<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.06.16
 * Time: 20:06
 */

namespace CoreBundle\Model\Event\Call;

use CoreBundle\Model\Call\CallContainerAwareTrait;
use CoreBundle\Model\Call\CallContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CallEvent
 * @package CoreBundle\Model\Event\Call
 */
class CallEvent extends Event implements CallContainerInterface
{
    use CallContainerAwareTrait;
}