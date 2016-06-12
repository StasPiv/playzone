<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.06.16
 * Time: 20:07
 */

namespace CoreBundle\Model\Call;

use CoreBundle\Entity\GameCall;

/**
 * Interface CallContainerInterface
 * @package CoreBundle\Model\Call
 */
interface CallContainerInterface
{
    /**
     * @return GameCall
     */
    public function getCall() : GameCall;
}