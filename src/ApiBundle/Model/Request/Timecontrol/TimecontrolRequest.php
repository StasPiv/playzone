<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:14
 */

namespace ApiBundle\Model\Request\Timecontrol;

use ApiBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Exception\Processor\TimecontrolProcessorException;

abstract class TimecontrolRequest implements RequestInterface
{
    protected $errorMessage = "Timecontrol request failed";

    /**
     * @param int $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     * @return ProcessorException
     */
    public function getException($code = 0, array $errors = [], $message = "", \Exception $previous = null)
    {
        return new TimecontrolProcessorException($this->errorMessage, $code, $errors, $previous);
    }

}