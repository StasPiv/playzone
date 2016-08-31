<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 16:33
 */

namespace CoreBundle\Model\Request;

/**
 * Interface RequestInterface
 * @package CoreBundle\Model\Request
 */
interface RequestInterface
{
    /**
     * @param string $ip
     * @return RequestInterface
     */
    public function setIp(string $ip);

    /**
     * @return string
     */
    public function getIp(): string ;
}