<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 18.01.16
 * Time: 13:25
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CallGetRequest
 * @package CoreBundle\Model\Request\Call
 */
class CallGetRequest extends CallRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *    message = "Enter call type"
     * )
     *
     * @Assert\Choice(choices = {"from", "to"}, message = "Call type is incorrect")
     */
    private $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

}