<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.05.16
 * Time: 23:28
 */

namespace CoreBundle\Entity;

use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Game
 *
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="event_name", type="string", nullable=false)
     */
    private $eventName;

    /**
     * @var EventInterface
     *
     * @ORM\Column(name="event_model", type="object", nullable=false)
     */
    private $eventModel;

    /**
     * @var string
     *
     * @ORM\Column(name="event_command_type", type="string", nullable=false)
     */
    private $eventCommandService;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_run", type="datetime", nullable=true)
     */
    private $lastRun;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_run", type="datetime", nullable=true)
     */
    private $nextRun;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $frequency;
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     * @return Event
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * @return EventInterface
     */
    public function getEventModel() : EventInterface 
    {
        return $this->eventModel;
    }

    /**
     * @param EventInterface $eventModel
     * @return Event
     */
    public function setEventModel(EventInterface $eventModel)
    {
        $this->eventModel = $eventModel;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventCommandService()
    {
        return $this->eventCommandService;
    }

    /**
     * @param string $eventCommandService
     * @return Event
     */
    public function setEventCommandService($eventCommandService)
    {
        $this->eventCommandService = $eventCommandService;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastRun() : \DateTime
    {
        return $this->lastRun;
    }

    /**
     * @param \DateTime $lastRun
     * @return Event
     */
    public function setLastRun(\DateTime $lastRun)
    {
        $this->lastRun = $lastRun;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNextRun() : \DateTime
    {
        return $this->nextRun;
    }

    /**
     * @param \DateTime $nextRun
     * @return Event
     */
    public function setNextRun(\DateTime $nextRun)
    {
        $this->nextRun = $nextRun;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return Event
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }
}