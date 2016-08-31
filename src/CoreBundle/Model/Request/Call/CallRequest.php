<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:08
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\CallProcessorException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Request\RequestTrait;

abstract class CallRequest implements RequestInterface
{
    use RequestTrait;
}