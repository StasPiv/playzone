<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:27
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use JMS\Serializer\SerializerBuilder;

class UserService
{
    const CURRENT_USER = 'current_user';
    use ContainerAwareTrait;

    private $manager;

    private $serializer;

    /**
     * UserService constructor.
     * @param Container $container
     * @param EntityManager $manager
     */
    public function __construct(Container $container, EntityManager $manager)
    {
        $this->setContainer($container);
        $this->manager = $manager;
        $this->serializer = SerializerBuilder::create()->build();
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
        if ($this->container->get('kernel')->getEnvironment() != 'test') {
            $this->manager->persist($user);
            $this->manager->flush();
        }
        $this->setCurrentUser($user);
    }

    /**
     * @param array $criteria
     * @return array|\CoreBundle\Entity\User[]
     */
    public function getUsers(array $criteria = [])
    {
        return $this->manager->getRepository('CoreBundle:User')->findBy($criteria);
    }

    public function getCurrentUser()
    {
        return $this->container->get("session")->get(self::CURRENT_USER);
    }

    /**
     * @param User $user
     */
    public function setCurrentUser(User $user)
    {
        $this->container->get('session')->set(self::CURRENT_USER,
            json_decode($this->serializer->serialize($user, 'json'), true));
    }
}