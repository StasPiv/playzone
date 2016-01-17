<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:08
 */

namespace ApiBundle\Model\Request\Call;

use ApiBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\CallProcessorException;
use CoreBundle\Exception\Processor\ProcessorException;

abstract class CallRequest implements RequestInterface
{
    protected $errorMessage = "Call request failed";

    /**
     * @param int $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     * @return ProcessorException
     */
    public function getException($code = 0, array $errors = [], $message = "", \Exception $previous = null)
    {
        return new CallProcessorException($this->errorMessage, $code, $errors, $previous);
    }
}