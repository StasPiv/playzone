<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:53
 */

namespace CoreBundle\Model\Request\Tournament;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Request\RequestTrait;

/**
 * Class TournamentRequest
 * @package CoreBundle\Model\Request\Tournament
 */
abstract class TournamentRequest implements RequestInterface
{
    use RequestTrait;
}