<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:38
 */

namespace WebsocketServerBundle\Model\Signaling\ServerMessage;

use WebsocketServerBundle\Model\Signaling\ServerMessageAction;
use WebsocketServerBundle\Model\Signaling\ServerMessageInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class AnswerFromSubscriberServerMessage implements ServerMessageInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $action = ServerMessageAction::ANSWER_FROM_SUBSCRIBER;

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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     * @return AnswerFromSubscriberServerMessage
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnswerSdpDescription()
    {
        return $this->answerSdpDescription;
    }

    /**
     * @param string $answerSdpDescription
     * @return AnswerFromSubscriberServerMessage
     */
    public function setAnswerSdpDescription($answerSdpDescription)
    {
        $this->answerSdpDescription = $answerSdpDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param string $candidate
     * @return AnswerFromSubscriberServerMessage
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;

        return $this;
    }
}