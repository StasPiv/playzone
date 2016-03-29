<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.03.16
 * Time: 0:09
 */
namespace CoreBundle\Model\Request;

use CoreBundle\Exception\Processor\ProcessorException;

interface RequestErrorInterface
{
    public function addError(string $key, string $errorMessage);

    /**
     * @return array
     */
    public function getErrors() : array;

    /**
     * @param $code
     * @throws ProcessorException
     */
    public function throwException($code);
}