<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\RequestErrorInterface;
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
    use ErrorAwareTrait;

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
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
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
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest) : User
    {
        if ($this->repository->findOneByEmail($registerRequest->getEmail())) {
            $this->getRequestError()->addError("email", 'This email was already registered');
        }

        if ($this->repository->findOneByLogin($registerRequest->getLogin())) {
            $this->getRequestError()->addError("login", 'This login was already registered');
        }

        if ($registerRequest->getPassword() != $registerRequest->getPasswordRepeat()) {
            $this->getRequestError()->addError("password_repeat", 'The password repeat should be the same');
        }

        $this->container->get("core.service.error")->throwExceptionIfHasErrors($this->getRequestError(), ResponseStatusCode::FORBIDDEN);

        $user = new User();

        $user->setLogin($registerRequest->getLogin())
             ->setEmail($registerRequest->getEmail())
             ->setPassword($this->generatePasswordHash($registerRequest->getPassword()));

        $this->saveUser($user);

        $this->generateUserToken($user);

        return $user;
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest) : User
    {
        if ($authRequest->getToken()) {
            return $this->container->get("core.service.security")->getUserIfCredentialsIsOk($authRequest, $this->getRequestError());
        }

        $user = $this->tryToFindUserInBothDatabases($authRequest);

        if ($user->getPassword() != $this->generatePasswordHash($authRequest->getPassword())) {
            $this->getRequestError()->addError("password", "The password is not correct");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $this->container->get("core.service.error")->throwExceptionIfHasErrors($this->getRequestError(), ResponseStatusCode::FORBIDDEN);

        $this->generateUserToken($user);

        return $user;
    }

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest) : array 
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
     * @param string $login
     * @param string $token
     * @return User
     * @throws UserNotFoundException
     * @throws TokenNotCorrectException
     */
    public function getUserByLoginAndToken(string $login, string $token) : User
    {
        $user = $this->repository->findOneByLogin($login);

        if (!$user instanceof User) {
            throw new UserNotFoundException;
        }

        $this->generateUserToken($user);

        if ($user->getToken() !== $token) {
            throw new TokenNotCorrectException;
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
     * @return User
     */
    private function tryToFindUserInBothDatabases(UserPostAuthRequest $authRequest) : User
    {
        $user = $this->repository->findOneByLogin($authRequest->getLogin());

        if ($user instanceof User) {
            return $user;
        }

        try {
            $user = $this->container->get('core.service.immortalchessnet')
                ->getUser($authRequest->getLogin(),$authRequest->getPassword());

            $user->setPassword($this->generatePasswordHash($authRequest->getPassword()));
            $this->saveUser($user);

            return $user;
        } catch (UserNotFoundException $e) {
            $this->getRequestError()->addError("login", "The login is not found");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (PasswordNotCorrectException $e) {
            $this->getRequestError()->addError("password", "The password is not correct");
            $this->getRequestError()->throwException(ResponseStatusCode::FORBIDDEN);
        }
    }

    /**
     * @param User $user
     */
    private function generateUserToken(User $user)
    {
        $user->setToken(md5($user->getLogin() . $user->getPassword()));
    }
}