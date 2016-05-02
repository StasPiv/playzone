<?php

namespace CoreBundle\Repository;

use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $login
     * @return User
     * @throws UserNotFoundException
     */
    public function findOneByLogin(string $login) : User
    {
        $user = parent::findOneBy(['login' => $login]);

        if (!$user instanceof User) {
            throw new UserNotFoundException;
        }

        return $user;
    }

    /**
     * @param string $email
     * @return User
     * @throws UserNotFoundException
     */
    public function findOneByEmail(string $email) : User
    {
        $user = parent::findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            throw new UserNotFoundException;
        }

        return $user;
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return User The entity instance or NULL if the entity can not be found.
     * @throws UserNotFoundException
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $user = parent::find($id, $lockMode, $lockVersion);

        if (!$user) {
            throw new UserNotFoundException;
        }

        return $user;
    }
}
