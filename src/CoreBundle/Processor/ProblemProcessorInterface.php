<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:19
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Problem;
use CoreBundle\Entity\UserProblem;
use CoreBundle\Model\Request\Problem\ProblemGetRandomRequest;
use CoreBundle\Model\Request\Problem\ProblemGetRequest;
use CoreBundle\Model\Request\Problem\ProblemPostSolutionRequest;

/**
 * Interface ProblemProcessorInterface
 * @package CoreBundle\Processor
 */
interface ProblemProcessorInterface extends ProcessorInterface
{
    /**
     * @param ProblemGetRandomRequest $request
     * @return Problem
     */
    public function processGetRandom(ProblemGetRandomRequest $request) : UserProblem;

    /**
     * @param ProblemGetRequest $request
     * @return Problem
     */
    public function processGet(ProblemGetRequest $request) : Problem;

    /**
     * @param ProblemPostSolutionRequest $request
     * @return UserProblem
     */
    public function processPostSolution(ProblemPostSolutionRequest $request) : UserProblem;
}