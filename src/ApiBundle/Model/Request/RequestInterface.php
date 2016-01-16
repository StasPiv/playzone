<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 16:33
 */

namespace ApiBundle\Model\Request;

use CoreBundle\Exception\Processor\ProcessorException;

interface RequestInterface
{
    /**
     * @param int $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     * @return ProcessorException
     */
    public function getException($code = 0, array $errors = [], $message = "", \Exception $previous = null);
}