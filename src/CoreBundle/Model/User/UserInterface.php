<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.09.16
 * Time: 21:57
 */

namespace CoreBundle\Model\User;

/**
 * Interface UserInterface
 * @package CoreBundle\Model\User
 */
interface UserInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getLogin(): string;

    /**
     * @return string
     */
    public function getEmail(): string;
}