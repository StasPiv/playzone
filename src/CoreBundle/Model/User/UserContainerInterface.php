<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.06.16
 * Time: 23:35
 */

namespace CoreBundle\Model\User;

use CoreBundle\Entity\User;

/**
 * Interface UserContainerInterface
 * @package CoreBundle\Model\User
 */
interface UserContainerInterface
{
    /**
     * @return User
     */
    public function getUser() : User;
}