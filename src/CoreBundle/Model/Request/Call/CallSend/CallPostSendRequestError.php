<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 29.03.16
 * Time: 22:46
 */

namespace CoreBundle\Model\Request\Call\CallSend;

use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallRequest;
use CoreBundle\Model\Request\RequestInterface;

class CallPostSendRequestErrorInterface extends CallRequest
{
    /**
     * @var array
     * 
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    private $errors;
    
    public function addError(string $key, string $errorMessage)
    {
        $this->errors[$key] = $errorMessage;
    }
}