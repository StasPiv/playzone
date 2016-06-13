<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 13:08
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Log;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Log\LogPostRequest;
use CoreBundle\Processor\LogProcessorInterface;
use CoreBundle\Repository\LogRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LogHandler
 * @package CoreBundle\Handler
 */
class LogHandler implements LogProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var LogRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Log');
    }

    /**
     * @param LogPostRequest $request
     * @return mixed
     */
    public function processPost(LogPostRequest $request) : Log
    {
        $me = $this->container->get("core.handler.user")->getSecureUser($request);

        $log = (new Log())->setMessage($request->getMessage());

        $this->container->get("logger")->debug($log->getMessage());
        //$this->saveEntity($log);

        return $log;
    }

    /**
     * @param Log $log
     */
    private function saveEntity(Log $log)
    {
        $this->manager->persist($log);
        $this->manager->flush();
    }


}