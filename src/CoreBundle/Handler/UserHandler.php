<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\User;
use CoreBundle\Processor\UserProcessorInterface;
use CoreBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
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
     * @return UserRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param UserPostRegisterRequest $registerRequest
     * @param UserPostRegisterRequest $registerError
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest, UserPostRegisterRequest $registerError)
    {
        if ($this->repository->findOneByEmail($registerRequest->getEmail())) {
            $registerError->setEmail('This email was already registered');
        }

        if ($this->repository->findOneByLogin($registerRequest->getLogin())) {
            $registerError->setLogin('This login was already registered');
        }

        if ($registerRequest->getPassword() != $registerRequest->getPasswordRepeat()) {
            $registerError->setPasswordRepeat('The password repeat should be the same');
        }

        $this->container->get("core.service.error")->throwExceptionIfHasErrors($registerError, ResponseStatusCode::FORBIDDEN);

        $user = new User();

        $user->setLogin($registerRequest->getLogin())
             ->setEmail($registerRequest->getEmail())
             ->setPassword($this->generatePasswordHash($registerRequest->getPassword()));

        $this->saveUser($user);

        return $user;
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @param UserPostAuthRequest $authError
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest, UserPostAuthRequest $authError) : User
    {
        if ($authRequest->getToken()) {
            return $this->container->get("core.service.security")->getUserIfCredentialsIsOk($authRequest, $authError);
        }

        $user = $this->tryToFindUserInBothDatabases($authRequest, $authError);

        if ($user->getPassword() != $this->generatePasswordHash($authRequest->getPassword())) {
            $authError->setPassword("The password is not correct");
            $authError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->container->get("core.service.error")->throwExceptionIfHasErrors($authError, ResponseStatusCode::FORBIDDEN);

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
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param $login
     * @param $token
     * @return User
     */
    public function getUserByLoginAndToken($login, $token)
    {
        $user = $this->repository->findOneByLogin($login);

        if (!$user instanceof User) {
            return null;
        }

        if ($user->getToken() !== $token) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $password
     * @return string
     */
    private function generatePasswordHash($password)
    {
        return md5($password);
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @param UserPostAuthRequest $authError
     * @return User
     */
    private function tryToFindUserInBothDatabases(UserPostAuthRequest $authRequest, UserPostAuthRequest $authError) : User
    {
        $user = $this->repository->findOneByLogin($authRequest->getLogin());

        if ($user instanceof User) {
            return $user;
        }

        $user = $this->container->get('core.service.immortalchessnet')
                     ->getUser($authRequest->getLogin(),$authRequest->getPassword());

        if (!$user->getEmail()) {
            $authError->setLogin("The login is not found");
            $authError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $user->setPassword($this->generatePasswordHash($authRequest->getPassword()));
        $this->saveUser($user);

        return $user;
    }
}