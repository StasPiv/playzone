<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:07
 */

namespace WebsocketServerBundle\Model\Signaling\ClientMessage;

use WebsocketServerBundle\Model\Signaling\ClientMessageAction;
use WebsocketServerBundle\Model\Signaling\ClientMessageInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriberSendDataMessage implements ClientMessageInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $action;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $room;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $answerSdpDescription;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $candidate;

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAnswerSdpDescription()
    {
        return $this->answerSdpDescription;
    }

    /**
     * @return string
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param string $action
     * @return SubscriberSendDataMessage
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $room
     * @return SubscriberSendDataMessage
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @param string $name
     * @return SubscriberSendDataMessage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $answerSdpDescription
     * @return SubscriberSendDataMessage
     */
    public function setAnswerSdpDescription($answerSdpDescription)
    {
        $this->answerSdpDescription = $answerSdpDescription;

        return $this;
    }

    /**
     * @param string $candidate
     * @return SubscriberSendDataMessage
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;

        return $this;
    }

    /**
     * @return ClientMessageAction
     */
    public function getAction()
    {
        return ClientMessageAction::SUBSCRIBER_SEND_DATA();
    }

}