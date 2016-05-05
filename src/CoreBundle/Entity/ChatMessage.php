<?php

namespace CoreBundle\Entity;

use CoreBundle\Model\ChatMessage\ChatMessageType;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ChatMessage
 *
 * @ORM\Table(name="chat_message", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ChatMessageRepository")
 */
class ChatMessage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     * @JMS\SerializedName("user")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var ChatMessageType
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * ChatMessage constructor.
     */
    public function __construct()
    {
        $this->time = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return ChatMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return ChatMessage
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime() : \DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return ChatMessage
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return ChatMessageType
     */
    public function getType() : ChatMessageType
    {
        return $this->type;
    }

    /**
     * @param ChatMessageType $type
     * @return ChatMessage
     */
    public function setType(ChatMessageType $type)
    {
        $this->type = $type->getValue();

        return $this;
    }
}

