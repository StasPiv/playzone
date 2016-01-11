<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 22:39
 */

namespace CoreBundle\Processor;


interface TimecontrolProcessorInterface extends ProcessorInterface
{
    /**
     * @return array
     */
    public function processGet();
}