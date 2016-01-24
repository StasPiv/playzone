<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 21:17
 */

namespace WebsocketServerBundle\Model\Message\Client\Call;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ClientMessageCallDecline
{
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $callId;

    /**
     * @return int
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * @param int $callId
     */
    public function setCallId($callId)
    {
        $this->callId = $callId;
    }
}