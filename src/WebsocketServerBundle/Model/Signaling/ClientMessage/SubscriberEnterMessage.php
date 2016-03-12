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

class SubscriberEnterMessage implements ClientMessageInterface
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
     * @param string $action
     * @return SubscriberEnterMessage
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $room
     * @return SubscriberEnterMessage
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @param string $name
     * @return SubscriberEnterMessage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ClientMessageAction
     */
    public function getAction()
    {
        return ClientMessageAction::SUBSCRIBER_ENTER();
    }

}