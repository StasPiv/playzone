<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 22:41
 */

namespace CoreBundle\Handler;

use CoreBundle\Processor\TimecontrolProcessorInterface;
use CoreBundle\Repository\TimecontrolRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class TimecontrolHandler implements TimecontrolProcessorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var TimecontrolRepository
     */
    private $repository;

    public function __construct(Container $container, EntityManager $manager)
    {
        $this->setContainer($container);
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Timecontrol');
    }

    /**
     * @return array
     */
    public function processGet()
    {
        return $this->repository->findAll();
    }
}