<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 22:35
 */

namespace ApiBundle\Tests\Controller;

class TimecontrolControllerTest extends BaseControllerTest
{
    public function testGet()
    {
        $this->testFromJson('timecontrols');
    }
}