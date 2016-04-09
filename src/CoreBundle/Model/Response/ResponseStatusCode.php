<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 19:14
 */

namespace CoreBundle\Model\Response;

/**
 * Class ResponseStatusCode
 * @package CoreBundle\Model\Response
 */
class ResponseStatusCode
{
    const OK = 200;
    
    const CREATED = 201;

    const BAD_FORMAT = 400;

    const FORBIDDEN = 403;

    const NOT_FOUND = 404;

    const ISE = 500;
}