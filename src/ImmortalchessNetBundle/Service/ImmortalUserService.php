<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.06.16
 * Time: 0:31
 */

namespace ImmortalchessNetBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\User\UserInterface;
use ImmortalchessNetBundle\Entity\ImmortalUser;
use ImmortalchessNetBundle\Repository\ImmortalUserRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ImmortalUserService
 * @package ImmortalchessNetBundle\Service
 */
class ImmortalUserService
{
    use ContainerAwareTrait;

    /**
     * @param string $loginOrEmail
     * @param string $password
     * @return UserInterface
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    public function getUser(string $loginOrEmail, string $password) : UserInterface
    {
        try {
            $immortalUser = $this->getRepository()->findOneByUsername($loginOrEmail);
        } catch (UserNotFoundException $e) {
            $immortalUser = $this->getRepository()->findOneByEmail($loginOrEmail);
        }

        if (!$this->checkPassword($immortalUser, $password)) {
            throw new PasswordNotCorrectException;
        }

        return $immortalUser;
    }

    /**
     * @param ImmortalUser $immortalUser
     * @param string $password
     * @return bool
     */
    private function checkPassword(ImmortalUser $immortalUser, string $password) : bool
    {
        return $immortalUser->getPassword() === md5(md5($password) . $immortalUser->getSalt());
    }

    /**
     * @return ImmortalUserRepository
     */
    private function getRepository()
    {
        return $this->container->get("doctrine")->getManager("immortalchess")
            ->getRepository("ImmortalchessNetBundle:ImmortalUser");
    }
}