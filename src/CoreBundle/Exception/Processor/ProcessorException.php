<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:14
 */

namespace CoreBundle\Exception\Processor;

use CoreBundle\Model\Request\RequestError;
use CoreBundle\Model\Request\RequestInterface;
use Exception;
use RuntimeException;

class ProcessorException extends RuntimeException
{
    /**
     * @var RequestInterface
     */
    private $requestError;


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param RequestError $requestError
     * @param int $code [optional] The Exception code.
     * @param string $message [optional] The Exception message to throw.
     */
    public function __construct(RequestError $requestError, $code, $message = "")
    {
        $this->setRequestError($requestError);
        parent::__construct($message, $code);
    }

    /**
     * @return RequestError
     */
    public function getRequestError() : RequestError
    {
        return $this->requestError;
    }

    /**
     * @param RequestError $requestError
     */
    public function setRequestError(RequestError $requestError)
    {
        $this->requestError = $requestError;
    }

}