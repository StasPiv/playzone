<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:06
 */

namespace WebsocketServerBundle\Model\Signaling\ClientMessage;

use WebsocketServerBundle\Model\Signaling\ClientMessageAction;
use WebsocketServerBundle\Model\Signaling\ClientMessageInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OwnerEnterMessage implements ClientMessageInterface
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
    private $offerSdpDescription;

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
    public function getOfferSdpDescription()
    {
        return $this->offerSdpDescription;
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
     * @return OwnerEnterMessage
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $room
     * @return OwnerEnterMessage
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @param string $offerSdpDescription
     * @return OwnerEnterMessage
     */
    public function setOfferSdpDescription($offerSdpDescription)
    {
        $this->offerSdpDescription = $offerSdpDescription;

        return $this;
    }

    /**
     * @param string $candidate
     * @return OwnerEnterMessage
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
        return ClientMessageAction::OWNER_ENTER();
    }

}