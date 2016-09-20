<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:42
 */

namespace CoreBundle\Exception\Handler;

use CoreBundle\Exception\Processor\ProcessorExceptionInterface;

class HandlerException extends \RuntimeException implements ProcessorExceptionInterface
{

}