<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.03.16
 * Time: 23:47
 */

namespace CoreBundle\Exception\Handler\User;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PasswordNotCorrectException extends UserHandlerException
{
    /**
     * @inheritDoc
     */
    public function __construct($message = 'Password is not correct', $code = Response::HTTP_FORBIDDEN, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}