<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 13:06
 */

namespace CoreBundle\Model\Request\Log;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LogPostRequest
 * @package CoreBundle\Model\Request\Log
 */
class LogPostRequest extends LogRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     * 
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return LogPostRequest
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}