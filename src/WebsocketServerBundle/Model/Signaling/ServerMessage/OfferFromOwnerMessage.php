<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.03.16
 * Time: 23:37
 */

namespace WebsocketServerBundle\Model\Signaling\ServerMessage;

use WebsocketServerBundle\Model\Signaling\ServerMessageAction;
use WebsocketServerBundle\Model\Signaling\ServerMessageInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OfferFromOwnerMessage implements ServerMessageInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     */
    private $action = ServerMessageAction::OFFER_FROM_OWNER;

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
    private $candidate;

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
     * @return OfferFromOwnerMessage
     */
    public function setRoom($room)
    {
        $this->room = $room;

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
     * @return OfferFromOwnerMessage
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOfferSdpDescription()
    {
        return $this->offerSdpDescription;
    }

    /**
     * @param string $offerSdpDescription
     * @return OfferFromOwnerMessage
     */
    public function setOfferSdpDescription($offerSdpDescription)
    {
        $this->offerSdpDescription = $offerSdpDescription;

        return $this;
    }
}