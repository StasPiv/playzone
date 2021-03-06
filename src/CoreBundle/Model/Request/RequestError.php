<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 29.03.16
 * Time: 22:55
 */

namespace CoreBundle\Model\Request;

use CoreBundle\Exception\Processor\ProcessorException;
use JMS\Serializer\Annotation as JMS;

class RequestError implements RequestErrorInterface
{
    /**
     * @var array
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    private $errors;

    /**
     * @param string $key
     * @param string $errorMessage
     * @return RequestErrorInterface
     */
    public function addError(string $key, string $errorMessage) : RequestErrorInterface
    {
        $this->errors[$key] = $errorMessage;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param $code
     * @throws ProcessorException
     */
    public function throwException($code)
    {
        throw new ProcessorException($this, $code, json_encode($this->errors));
    }
}