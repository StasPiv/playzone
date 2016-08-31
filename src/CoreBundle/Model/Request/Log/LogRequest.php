<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 13:04
 */

namespace CoreBundle\Model\Request\Log;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Request\RequestTrait;

/**
 * Class LogRequest
 * @package CoreBundle\Model\Request\Log
 */
abstract class LogRequest implements RequestInterface 
{
    use RequestTrait;
}