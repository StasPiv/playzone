<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use ApiBundle\Model\Request\User\UserGetListRequest;
use ApiBundle\Model\Request\User\UserPostAuthRequest;
use ApiBundle\Model\Request\User\UserPostRegisterRequest;
use ApiBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Processor\UserProcessorException;
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
     * @param UserPostRegisterRequest $registerRequest
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest)
    {
        $errors = [];

        if ($this->repository->findBy(['email' => $registerRequest->getEmail()])) {
            $errors['email'] = 'This email was already registered';
        }

        if ($this->repository->findBy(['login' => $registerRequest->getLogin()])) {
            $errors['login'] = 'This login was already registered';
        }

        if ($registerRequest->getPassword() != $registerRequest->getPasswordRepeat()) {
            $errors['password_repeat'] = 'The password repeat should be the same';
        }

        if (!empty($errors)) {
            throw new UserProcessorException("Registration failed", ResponseStatusCode::FORBIDDEN, $errors);
        }

        $user = new User();

        $user->setLogin($registerRequest->getLogin());
        $user->setEmail($registerRequest->getEmail());
        $user->setPassword(md5($registerRequest->getPassword()));

        $this->saveUser($user);

        return $user;
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest)
    {
        $user = $this->repository->findOneBy(['login' => $authRequest->getLogin()]);

        if (!$user instanceof User) {
            throw new UserProcessorException("Authorization failed", ResponseStatusCode::FORBIDDEN,
                ["login" => "The login is not found"]);
        }

        if ($user->getPassword() != md5($authRequest->getPassword())) {
            throw new UserProcessorException("Authorization failed", ResponseStatusCode::FORBIDDEN,
                ["password" => "The password is not correct"]);
        }

        $user->setToken($this->generateValidUserToken($user));

        return $user;
    }

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest)
    {
        // TODO: need to add conditions here
        return $this->repository->findAll();
    }

    /**
     * @param User $user
     */
    private function saveUser(User $user)
    {
        if ($this->container->get('kernel')->getEnvironment() != 'test') {
            $this->manager->persist($user);
            $this->manager->flush();
        }
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateValidUserToken($user)
    {
        return md5($user->getLogin() . $user->getPassword());
    }

    /**
     * @param $login
     * @param $token
     * @return User
     */
    public function getUserByLoginAndToken($login, $token)
    {
        $user = $this->repository->findOneBy(['login' => $login]);

        if (!$user instanceof User) {
            return null;
        }

        if ($this->generateValidUserToken($user) !== $token) {
            return null;
        }

        return $user;
    }

}