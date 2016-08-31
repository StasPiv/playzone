<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:27
 */

namespace CoreBundle\Model\Request\Problem;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Request\RequestTrait;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProblemGetRandomRequest
 * @package CoreBundle\Model\Request\Problem
 */
class ProblemGetRandomRequest implements RequestInterface, SecurityRequestInterface
{

    use SecurityRequestAwareTrait, RequestTrait;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $login = '';

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $token = '';

    /**
     * ProblemGetRandomRequest constructor.
     */
    public function __construct()
    {
    }
}