<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:18
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Problem;
use CoreBundle\Entity\User;
use CoreBundle\Entity\UserProblem;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Problem\ProblemGetRandomRequest;
use CoreBundle\Model\Request\Problem\ProblemGetRequest;
use CoreBundle\Model\Request\Problem\ProblemPostSolutionRequest;
use CoreBundle\Processor\ProblemProcessorInterface;
use CoreBundle\Repository\LogRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
    public function processPostSolution(ProblemPostSolutionRequest $request) : UserProblem
    {
        $secureUser = $this->container->get('core.handler.user')->getSecureUser($request);

        $problem = $this->repository->find($request->getId());

        $userProblem = $this->getUserProblem($secureUser, $problem, 1);

        if (strpos($userProblem->getProblem()->getPgn(), $request->getSolution()) !== false) {
            $userProblem->setSolved($userProblem->getSolved() + 1);
            $userProblem->setCorrect(true);
        } else {
            $request->setTime($userProblem->getTime() * 2);
        }

        $userProblem->setTime($request->getTime());

        $this->manager->persist($userProblem);
        $this->manager->flush();

        $this->mixTotals($secureUser, $userProblem);

        return $userProblem;
    }

    /**
     * @inheritDoc
     */
    public function processGetRandom(ProblemGetRandomRequest $request) : UserProblem
    {
        $countRows = $this->repository->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->getQuery()
            ->getResult();

        $problems = $this->repository->findBy([], [], 1, mt_rand(0, $countRows[0][1] - 1));

        $problem = $problems[0];

        try {
            $secureUser = $this->container->get('core.handler.user')->getSecureUser($request);
        } catch (ProcessorException $e) {
            return (new UserProblem())->setProblem($problem);
        }

        $userProblem = $this->getUserProblem($secureUser, $problem);

        $userProblem->setTotal($userProblem->getTotal() + 1);

        $this->manager->persist($userProblem);
        $this->manager->flush();

        $this->mixTotals($secureUser, $userProblem);

        return $userProblem;
    }

    /**
     * @return EntityRepository
     */
    private function getUserProblemRepository()
    {
        return $this->container->get('doctrine')->getRepository('CoreBundle:UserProblem');
    }

    /**
     * @param User $user
     * @param Problem $problem
     * @param int $defaultTotal
     * @return UserProblem
     */
    private function getUserProblem(User $user, Problem $problem, $defaultTotal = 0) : UserProblem
    {
        $userProblem = $this->getUserProblemRepository()
            ->findOneBy(['user' => $user, 'problem' => $problem]);

        if (!$userProblem) {
            $myProblem = $this->getUserTotals($user);
            $userProblem = new UserProblem();
            $userProblem->setUser($user)
                        ->setProblem($problem)
                        ->setTotal($defaultTotal);
            if (isset($myProblem['time'])) {
                $userProblem->setTime($myProblem['time']);
            }
        }

        return $userProblem;
    }

    /**
     * @param User $user
     * @param UserProblem $userProblem
     */
    private function mixTotals(User $user, UserProblem $userProblem)
    {
        $myProblem = $this->getUserTotals($user);

        $userProblem->setSolved((int)$myProblem['solved'])
            ->setTotal((int)$myProblem['total'])
            ->setTime($myProblem['time']);
    }

    /**
     * @param User $user
     * @return mixed
     */
    private function getUserTotals(User $user)
    {
        $myProblems = $this->getUserProblemRepository()->createQueryBuilder('up')
            ->select('SUM(up.total) as total, SUM(up.solved) as solved, AVG(up.time) as time')
            ->where('up.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $myProblem = $myProblems[0];

        return $myProblem;
    }
}