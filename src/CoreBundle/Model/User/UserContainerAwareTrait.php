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
 * Class UserContainerAwareTrait
 * @package CoreBundle\Model\User
 */
trait UserContainerAwareTrait
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserContainerAwareTrait
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}