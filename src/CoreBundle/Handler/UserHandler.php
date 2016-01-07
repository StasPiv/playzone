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
    public function processRegister(array $userData)
    {
        $this->userService->setData($user = new User(), $userData);

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
            throw new ProcessorException("Registration failed", 403, $errors);
        }

        $this->userService->saveUser($user);

        return $user;
    }

}