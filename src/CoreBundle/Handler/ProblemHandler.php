<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:18
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Problem;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Problem\ProblemGetRandomRequest;
use CoreBundle\Model\Request\Problem\ProblemGetRequest;
use CoreBundle\Processor\ProblemProcessorInterface;
use CoreBundle\Repository\LogRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ProblemHandler
 * @package CoreBundle\Handler
 */
class ProblemHandler implements ProblemProcessorInterface
{
    use ContainerAwareTrait, ErrorAwareTrait;

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
        $this->repository = $this->manager->getRepository('CoreBundle:Problem');
    }

    /**
     * @inheritDoc
     */
    public function processGet(ProblemGetRequest $request) : Problem
    {
        return $this->repository->find($request->getId());
    }

    /**
     * @inheritDoc
     */
    public function processGetRandom(ProblemGetRandomRequest $request) : Problem
    {
        $problems = $this->repository->findAll();

        return $problems[mt_rand(0, count($problems) - 1)];
    }
}