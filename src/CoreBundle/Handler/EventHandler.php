<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 19:52
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Event;
use CoreBundle\Exception\Handler\Event\EventFrequencyEmptyException;
use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Repository\EventRepository;
use Cron\CronExpression;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class EventHandler
 * @package CoreBundle\Handler
 */
class EventHandler
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * ChatHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Event');
    }

    /**
     * @return EventRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param EventInterface $eventModel
     * @param string $commandService
     */
    public function initEventAndSave(EventInterface $eventModel, string $commandService)
    {
        $event = new Event();
        
        $event->setEventName($eventModel->getName())
              ->setFrequency($eventModel->getFrequency());
        
        if (!$this->container->get($commandService) instanceof EventCommandInterface) {
            throw new \RuntimeException("service $commandService is not EventCommandInterface instance");
        }
        
        $event->setEventModel($eventModel)
              ->setEventCommandService($commandService);

        try {
            $this->initNextRun($event);
            $this->saveEvent($event);
        } catch (EventFrequencyEmptyException $e) {
            $this->saveEvent($event);
        }
    }

    public function runAllCurrentEvents()
    {
        foreach ($this->getEventsAtTheMoment() as $event) {
            $this->runEvent($event);
        }
    }

    /**
     * @param Event $event
     */
    public function saveEvent(Event $event)
    {
        $this->manager->persist($event);

        $this->manager->flush();
    }

    /**
     * @param Event $event
     */
    private function runEvent(Event $event)
    {
        /** @var EventCommandInterface $command */
        $command = $this->container->get($event->getEventCommandService());
        $command->setEventModel($event->getEventModel());
        $command->run();

        $event->setLastRun(new \DateTime());

        try {
            $this->initNextRun($event);
            $this->saveEvent($event);
        } catch (\RuntimeException $e) {
            $this->container->get("logger")->error($e->getMessage(), $e->getTrace());
            $this->manager->remove($event);
            $this->manager->flush();
        }
    }

    /**
     * @param Event $event
     * @throws EventFrequencyEmptyException
     */
    private function initNextRun(Event $event)
    {
        if (!$event->getFrequency()) {
            throw new EventFrequencyEmptyException;
        } 

        $cron = CronExpression::factory($event->getFrequency());
        $cron->isDue();
        $event->setNextRun($cron->getNextRunDate());
    }

    /**
     * @return Event[]
     */
    private function getEventsAtTheMoment() : array
    {
        return $this->repository->createQueryBuilder('e')
                    ->where('e.nextRun <= :now')
                    ->setParameter('now', new \DateTime())
                    ->getQuery()
                    ->getResult();
    }
}