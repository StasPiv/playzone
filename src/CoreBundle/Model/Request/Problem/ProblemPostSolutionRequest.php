<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 10.08.16
 * Time: 22:09
 */

namespace CoreBundle\Model\Request\Problem;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProblemPostSolutionRequest
 * @package CoreBundle\Model\Request\Problem
 */
class ProblemPostSolutionRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var int
     *
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @JMS\Type("string")
     */
    private $solution;

    /**
     * @var int
     *
     * @JMS\Type("integer")
     */
    private $time;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProblemPostSolutionRequest
     */
    public function setId(int $id): ProblemPostSolutionRequest
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSolution(): string
    {
        return $this->solution;
    }

    /**
     * @param string $solution
     * @return ProblemPostSolutionRequest
     */
    public function setSolution(string $solution): ProblemPostSolutionRequest
    {
        $this->solution = $solution;

        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return ProblemPostSolutionRequest
     */
    public function setTime(int $time): ProblemPostSolutionRequest
    {
        $this->time = $time;

        return $this;
    }
}