<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\User;
use CoreBundle\Exception\UserProcessorException;
use CoreBundle\Processor\UserProcessorInterface;
use CoreBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\ConstraintViolation;
use Doctrine\ORM\EntityManager;

class UserHandler implements UserProcessorInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var UserRepository
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
        $this->repository = $this->manager->getRepository('CoreBundle:User');
    }

    /**
     * @param array $userData
     * @return mixed
     */
    public function processPostRegister(array $userData)
    {
        $this->setData($user = new User(), $userData);

        $errors = [];

        foreach($this->container->get('validator')->validate($user) as $error) {
            /** @var ConstraintViolation $error */
            switch ($error->getPropertyPath()) {
                case 'rawPassword':
                    $errorKey = 'password';
                    break;
                default:
                    $errorKey = $error->getPropertyPath();
            }

            $errors[$errorKey] = $error->getMessage();
        }

        if ($this->repository->findBy(['email' => $user->getEmail()])) {
            $errors['email'] = 'This email was already registered';
        }

        if ($this->repository->findBy(['login' => $user->getLogin()])) {
            $errors['login'] = 'This login was already registered';
        }

        if (!isset($userData['password_repeat']) || isset($userData['password']) && $userData['password'] != $userData['password_repeat']) {
            $errors['password_repeat'] = 'The password repeat should be the same';
        }

        if (!empty($errors)) {
            throw new UserProcessorException("Registration failed", 403, $errors);
        }

        $this->saveUser($user);

        return $user;
    }

    /**
     * @param array $userData
     * @return array
     */
    public function processPostAuth(array $userData)
    {
        if (!isset($userData['login'])) {
            $errors["login"] = "Enter login";
            throw new UserProcessorException("Authorization failed", 403, $errors);
        }

        if (!isset($userData['password'])) {
            $errors["password"] = "Enter password";
            throw new UserProcessorException("Authorization failed", 403, $errors);
        }

        $users = $this->repository->findBy(['login' => $userData['login']]);

        if (!$users) {
            $errors["login"] = "The login is not found";
            throw new UserProcessorException("Authorization failed", 403, $errors);
        }

        /** @var User $user */
        $user = $users[0];

        if ($user->getPassword() != md5($userData['password'])) {
            $errors["password"] = "The password is not correct";
            throw new UserProcessorException("Authorization failed", 403, $errors);
        }

        $user->setToken(md5($user->getLogin() . $user->getPassword()));

        return $user;
    }

    /**
     * @return array
     */
    public function processGetList()
    {
        return $this->repository->findAll();
    }

    /**
     * @param User $user
     * @param array $userData
     */
    private function setData(User $user, array $userData)
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
    private function saveUser(User $user)
    {
        $user->setPassword(md5($user->getRawPassword()));
        if ($this->container->get('kernel')->getEnvironment() != 'test') {
            $this->manager->persist($user);
            $this->manager->flush();
        }
    }

}