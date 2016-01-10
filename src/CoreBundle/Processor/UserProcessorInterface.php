<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:07
 */

namespace CoreBundle\Processor;


interface UserProcessorInterface extends ProcessorInterface
{
    /**
     * @param array $userData
     * @return array
     */
    public function processPostRegister(array $userData);

    /**
     * @param array $userData
     * @return array
     */
    public function processPostAuth(array $userData);
}