<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:07
 */

namespace CoreBundle\Processor;


interface UserProcessorInterface
{
    /**
     * @param array $userData
     * @return mixed
     */
    public function processRegister(array $userData);
}