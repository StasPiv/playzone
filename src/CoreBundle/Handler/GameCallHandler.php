<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.16
 * Time: 21:29
 */

namespace CoreBundle\Handler;

use ApiBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\GameCallHandlerException;
use CoreBundle\Model\GameCall\GameCallType;
use CoreBundle\Repository\GameCallRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class GameCallHandler
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var GameCallRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param Container $container
     * @param EntityManager $manager
     */
    public function __construct(Container $container, EntityManager $manager)
    {
        $this->setContainer($container);
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:GameCall');
    }

    /**
     * @param User $user
     * @param $callType
     * @return GameCall[]
     */
    public function getCalls(User $user, $callType)
    {
        switch ($callType) {
            case GameCallType::FROM:
                $fieldForUser = 'fromUser';
                break;
            case GameCallType::TO:
                $fieldForUser = 'toUser';
                break;
            default:
                throw new GameCallHandlerException("Unknown call type for calls", ResponseStatusCode::FORBIDDEN);
        }

        $calls = $this->manager
            ->createQuery(
                "SELECT c FROM CoreBundle:GameCall c WHERE c.$fieldForUser = :user"
            )
            ->setParameter("user", $user)
            ->getResult();

        foreach ($calls as $call) {
            /** @var GameCall $call */
            $this->container->get("core.handler.game")->defineUserColorForGame($user, $call->getGame());
        }

        return $calls;
    }
}