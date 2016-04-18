<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\UserSetting;
use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Exception\Handler\User\UserSettingNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserPatchSettingRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\User;
use CoreBundle\Processor\UserProcessorInterface;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Repository\UserSettingRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\EntityManager;

/**
 * Class UserHandler
 * @package CoreBundle\Handler
 */
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
        try {
            if ($this->repository->findOneByEmail($registerRequest->getEmail())) {
                $this->getRequestError()->addError("email", 'This email was already registered');
            }
        } catch (UserNotFoundException $e) {
            // that's alright
        }

        try {
            if ($this->repository->findOneByLogin($registerRequest->getLogin())) {
                $this->getRequestError()->addError("login", 'This login was already registered');
            }
        } catch (UserNotFoundException $e) {
            // that's alright
        }

        if ($registerRequest->getPassword() !== $registerRequest->getPasswordRepeat()) {
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
            $user = $this->container->get("core.service.security")
                          ->getUserIfCredentialsIsOk($authRequest,$this->getRequestError());

            $this->initUserSettings($user);
            return $user;
        }

        try {
            try {
                $user = $this->searchUserOnPlayzone($authRequest);
            } catch (UserNotFoundException $e) {
                $user = $this->searchUserOnImmortalchess($authRequest);
                $this->saveUser($user);
            }
        } catch (UserNotFoundException $e) {
            $this->getRequestError()->addError("login", "The login is not found")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (PasswordNotCorrectException $e) {
            $this->getRequestError()->addError("password", "The password is not correct")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        /** @var User $user */
        $this->generateUserToken($user);
        $this->initUserSettings($user);

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
     * @param UserPatchSettingRequest $settingRequest
     * @return UserSetting
     */
    public function processPatchSetting(UserPatchSettingRequest $settingRequest) : UserSetting
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($settingRequest, $this->getRequestError());
        
        try {
            $userSetting = $this->manager->getRepository('CoreBundle:UserSetting')->find($settingRequest->getSettingId());
        } catch (UserSettingNotFoundException $e) {
            $this->getRequestError()
                 ->addError("setting_id", "Setting {$settingRequest->getSettingId()} is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var UserSetting $userSetting */
        $userSetting->setValue($settingRequest->getValue());
        $me->setSetting($userSetting);

        $this->saveUser($me);

        return $userSetting;
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
    private function generatePasswordHash($password) : string 
    {
        return md5($password);
    }

    /**
     * @param User $user
     */
    private function generateUserToken(User $user)
    {
        $user->setToken(md5($user->getLogin() . $user->getPassword()));
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    private function searchUserOnImmortalchess(UserPostAuthRequest $authRequest) : User
    {
        return $this->container->get('core.service.immortalchessnet')
                    ->getUser($authRequest->getLogin(), $authRequest->getPassword())
                    ->setPassword($this->generatePasswordHash($authRequest->getPassword()));
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    private function searchUserOnPlayzone(UserPostAuthRequest $authRequest) : User
    {
        $user = $this->repository->findOneByLogin($authRequest->getLogin());

        if ($user->getPassword() != $this->generatePasswordHash($authRequest->getPassword())) {
            throw new PasswordNotCorrectException;
        }

        return $user;
    }

    /**
     * @param User $user
     * @return void
     */
    private function initUserSettings(User $user)
    {
        $allSettings = $this->manager->getRepository("CoreBundle:UserSetting")->findAll();

        foreach ($allSettings as $setting) {
            try {
                $user->getSetting($setting->getName());
            } catch (UserSettingNotFoundException $e) {
                $user->setSetting($setting);
            }
        }
    }
}