<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:14
 */

namespace CoreBundle\Exception\Processor;

use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\RequestInterface;
use Exception;
use RuntimeException;

class ProcessorException extends RuntimeException implements ProcessorExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $RequestErrorInterface;


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param RequestErrorInterface $RequestErrorInterface
     * @param int $code [optional] The Exception code.
     * @param string $message [optional] The Exception message to throw.
     */
    public function __construct(RequestErrorInterface $RequestErrorInterface, $code, $message = "")
    {
        $this->setRequestErrorInterface($RequestErrorInterface);
        parent::__construct($message, $code);
    }

    /**
     * @return RequestErrorInterface
     */
    public function getRequestErrorInterface() : RequestErrorInterface
    {
        return $this->RequestErrorInterface;
    }

    /**
     * @param RequestErrorInterface $RequestErrorInterface
     */
    public function setRequestErrorInterface(RequestErrorInterface $RequestErrorInterface)
    {
        $this->RequestErrorInterface = $RequestErrorInterface;
    }

}