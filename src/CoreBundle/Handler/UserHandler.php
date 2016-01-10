<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\User;
use CoreBundle\Exception\ProcessorException;
use CoreBundle\Processor\UserProcessorInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\ConstraintViolation;

class UserHandler implements UserProcessorInterface
{
    use ContainerAwareTrait;

    const AUTHORIZATION_FAILED = "Authorization failed";
    const REGISTRATION_FAILED = "Registration failed";

    /**
     * @var \CoreBundle\Service\UserService
     */
    private $userService;

    /**
     * UserHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);
        $this->userService = $this->container->get("core.service.user");
    }

    /**
     * @param array $userData
     * @return mixed
     */
    public function processPostRegister(array $userData)
    {
        $this->setData($user = new User(), $userData);

        $validatorResult = $this->container->get('validator')->validate($user);
        $errors = [];

        foreach($validatorResult as $error) {
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

        if ($this->userService->getUsers(['email' => $user->getEmail()])) {
            $errors['email'] = 'This email was already registered';
        }

        if ($this->userService->getUsers(['login' => $user->getLogin()])) {
            $errors['login'] = 'This login was already registered';
        }

        if (!isset($userData['password_repeat']) || isset($userData['password']) && $userData['password'] != $userData['password_repeat']) {
            $errors['password_repeat'] = 'The password repeat should be the same';
        }

        if (!empty($errors)) {
            throw new ProcessorException(self::REGISTRATION_FAILED, 403, $errors);
        }

        $this->userService->saveUser($user);

        return $user;
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
     * @param array $userData
     * @return array
     */
    public function processPostAuth(array $userData)
    {
        if (!isset($userData['login'])) {
            $errors["login"] = "Enter login";
            throw new ProcessorException(self::AUTHORIZATION_FAILED, 403, $errors);
        }

        if (!isset($userData['password'])) {
            $errors["password"] = "Enter password";
            throw new ProcessorException(self::AUTHORIZATION_FAILED, 403, $errors);
        }

        $users = $this->userService->getUsers(['login' => $userData['login']]);

        if (!$users) {
            $errors["login"] = "The login is not found";
            throw new ProcessorException(self::AUTHORIZATION_FAILED, 403, $errors);
        }

        $user = $users[0];

        if ($user->getPassword() != md5($userData['password'])) {
            $errors["password"] = "The password is not correct";
            throw new ProcessorException(self::AUTHORIZATION_FAILED, 403, $errors);
        }

        return $user;
    }

}