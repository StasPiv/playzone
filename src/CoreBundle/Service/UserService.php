<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:27
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProcessorException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UserService
{
    use ContainerAwareTrait;

    private $manager;

    /**
     * UserService constructor.
     * @param Container $container
     * @param EntityManager $manager
     */
    public function __construct(Container $container, EntityManager $manager)
    {
        $this->setContainer($container);
        $this->manager = $manager;
    }

    public function setData(User $user, array $userData)
    {
        if (isset($userData['login'])) {
            $user->setLogin($userData['login']);
        }

        if (isset($userData['email'])) {
            $user->setEmail($userData['email']);
        }

        if (isset($userData['password'])) {
            $user->setRawPassword($userData['password']);
        }

    }

    /**
     * @param User $user
     */
    public function saveUser(User $user)
    {
        $user->setPassword(md5($user->getRawPassword()));
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param array $criteria
     * @return array|\CoreBundle\Entity\User[]
     */
    public function getUsers(array $criteria = [])
    {
        return $this->manager->getRepository('CoreBundle:User')->findBy($criteria);
    }
}