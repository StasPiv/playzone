<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 30.03.16
 * Time: 23:22
 */

namespace CoreBundle\Exception\Handler\User;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends UserHandlerException
{
    /**
     * @inheritDoc
     */
    public function __construct($message = 'User not found', $code = Response::HTTP_FORBIDDEN, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}