<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 12:55
 */

namespace ApiBundle\Tests\Controller;

/**
 * Class LogControllerTest
 * @package ApiBundle\Tests\Controller
 */
class LogControllerTest extends BaseControllerTest
{
    public function testIndex()
    {
        $this->assertFromJson('log');
    }
}