<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:00
 */

namespace ApiBundle\Tests\Controller;

/**
 * Class ProblemControllerTest
 * @package ApiBundle\Tests\Controller
 */
class ProblemControllerTest extends BaseControllerTest
{
    public function testGetProblem()
    {
        $this->assertFromJson('problems/{id}');
    }

    public function testGetRandomProblem()
    {
        $this->assertFromJson('problem/random');
    }

    public function testPostSolutionProblem()
    {
        $this->assertFromJson('problems/{id}/solutions');
    }
}