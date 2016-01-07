<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:14
 */

namespace CoreBundle\Exception;

use Exception;

class ProcessorException extends \RuntimeException
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = "", $code = 0, array $errors = [], Exception $previous = null)
    {
        if (!empty($errors)) {
            $this->setErrors($errors);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

}