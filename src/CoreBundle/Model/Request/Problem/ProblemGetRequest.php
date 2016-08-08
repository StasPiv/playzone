<?php

namespace CoreBundle\Model\Request\Problem;

use CoreBundle\Model\Request\RequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:17
 */
class ProblemGetRequest implements RequestInterface
{

    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     *
     * @Assert\NotBlank(
     *     message = "Id is required for this request"
     * )
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProblemGetRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}