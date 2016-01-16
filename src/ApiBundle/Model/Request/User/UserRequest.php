<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 19:55
 */

namespace ApiBundle\Model\Request\User;

use ApiBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Exception\Processor\UserProcessorException;

abstract class UserRequest implements RequestInterface
{
    protected $errorMessage = "User request failed";

    /**
     * @param int $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     * @return ProcessorException
     */
    public function getException($code = 0, array $errors = [], $message = "", \Exception $previous = null)
    {
        return new UserProcessorException($this->errorMessage, $code, $errors, $previous);
    }
}