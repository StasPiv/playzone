<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:14
 */

namespace CoreBundle\Model\Request\Chat;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChatPostMessageRequest
 * @package CoreBundle\Model\Request\Chat
 */
class ChatPostMessageRequest extends ChatRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Message is required for this request"
     * )
     */
    private $message;

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ChatPostMessageRequest
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
}