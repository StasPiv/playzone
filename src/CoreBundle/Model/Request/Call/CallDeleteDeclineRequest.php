<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 21:54
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CallDeleteDeclineRequest
 * @package CoreBundle\Model\Request\Call
 */
class CallDeleteDeclineRequest extends CallRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Call id is required for this request"
     * )
     */
    private $callId;

    /**
     * @return string
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * @param string $callId
     */
    public function setCallId($callId)
    {
        $this->callId = $callId;
    }
}