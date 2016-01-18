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

        $this->container->get("core.handler.error")->throwExceptionIfHasErrors($registerError, ResponseStatusCode::FORBIDDEN);

        $user = new User();

        $user->setLogin($registerRequest->getLogin());
        $user->setEmail($registerRequest->getEmail());
        $user->setPassword($this->generatePasswordHash($registerRequest->getPassword()));

        $this->saveUser($user);

        $user->setToken($this->generateValidUserToken($user));

        return $user;
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @param UserPostAuthRequest $authError
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest, UserPostAuthRequest $authError)
    {
        if ($authRequest->getToken()) {
            $user = $this->container->get("core.handler.security")->getMeIfCredentialsIsOk($authRequest, $authError);
            $user->setToken($this->generateValidUserToken($user));
            return $user;
        }

        $user = $this->repository->findOneByLogin($authRequest->getLogin());

        switch (true) {
            case !$user instanceof User:
                $authError->setLogin("The login is not found");
                break;
            case $user->getPassword() != $this->generatePasswordHash($authRequest->getPassword()):
                $authError->setPassword("The password is not correct");
                break;
        }

        $this->container->get("core.handler.error")->throwExceptionIfHasErrors($authError, ResponseStatusCode::FORBIDDEN);

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
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateValidUserToken($user)
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
        $user = $this->repository->findOneByLogin($login);

        if (!$user instanceof User) {
            return null;
        }

        if ($this->generateValidUserToken($user) !== $token) {
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

}