<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 21:05
 */

namespace CoreBundle\Model\Request;

/**
 * Interface SecurityRequestInterface
 * @package CoreBundle\Model\Request
 */
interface SecurityRequestInterface extends RequestInterface
{
    /**
     * @param string $login
     * @return string
     */
    public function setLogin($login);

    /**
     * @return string
     */
    public function getLogin();

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();
}