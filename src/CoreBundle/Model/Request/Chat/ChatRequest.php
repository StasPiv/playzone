<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:13
 */

namespace CoreBundle\Model\Request\Chat;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Request\RequestTrait;

/**
 * Class ChatRequest
 * @package CoreBundle\Model\Request\Chat
 */
abstract class ChatRequest implements RequestInterface
{
    use RequestTrait;
}