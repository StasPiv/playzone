<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:14
 */

namespace CoreBundle\Model\Request\Timecontrol;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Exception\Processor\TimecontrolProcessorException;

abstract class TimecontrolRequest implements RequestInterface
{
    /**
     * @param $code
     */
    public function throwException($code)
    {
        throw new TimecontrolProcessorException($this, $code);
    }

}