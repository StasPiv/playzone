<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 22:39
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Timecontrol;

interface TimecontrolProcessorInterface extends ProcessorInterface
{
    /**
     * @return Timecontrol[]
     */
    public function processGet();
}