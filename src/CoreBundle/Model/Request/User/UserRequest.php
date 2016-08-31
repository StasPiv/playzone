<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 19:55
 */

namespace CoreBundle\Model\Request\User;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Exception\Processor\UserProcessorException;
use CoreBundle\Model\Request\RequestTrait;

abstract class UserRequest implements RequestInterface
{
    use RequestTrait;
}