<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 16:33
 */

namespace CoreBundle\Model\Request;

interface RequestInterface
{

    /**
     * @param $code
     * @return
     */
    public function throwException($code);
}