<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.17
 * Time: 13:48
 */

namespace ImmortalchessNetBundle\Service\Promotion;

use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use Doctrine\Common\Persistence\ObjectManager;
use ImmortalchessNetBundle\Entity\ImmortalUser;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PromotionRegisteredToPlayersRule
 * @package ImmortalchessNetBundle\Service\Promotion
 */
class PromotionRegisteredToPlayersRule implements PromotionRule, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const REGISTERED_USER_GROUP_ID = 2;

    const SENIOR_GROUP_ID = 19;

    const PLAYER_GROUP_ID = 23;

    /**
     * @var array|User[]
     */
    private $usersToDemotion;

    /**
     * @var array|User[]
     */
    private $usersToPromotion;

    /**
     * @var array|User[]
     */
    private $usersToAdditionalDemotion;

    /**
     * @var array|User[]
     */
    private $usersToAdditionalPromotion;

    /**
     * @inheritDoc
     */
    public function getUsersToPromotion()
    {
        if (isset($this->usersToPromotion)) {
            return $this->usersToPromotion;
        }

        $this->fillPromotionUsers();

        return $this->usersToPromotion;
    }

    /**
     * @inheritDoc
     */
    public function getUsersToDemotion()
    {
        if (isset($this->usersToDemotion)) {
            return $this->usersToDemotion;
        }

        $this->fillDemotionUsers();

        return $this->usersToDemotion;
    }

    /**
     * @inheritDoc
     */
    public function getUsersToAdditionalPromotion()
    {
        if (isset($this->usersToAdditionalPromotion)) {
            return $this->usersToAdditionalPromotion;
        }

        $this->fillPromotionUsers();

        return $this->usersToAdditionalPromotion;
    }

    /**
     * @inheritDoc
     */
    public function getUsersToAdditionalDemotion()
    {
        if (isset($this->usersToAdditionalDemotion)) {
            return $this->usersToAdditionalDemotion;
        }

        $this->fillDemotionUsers();

        return $this->usersToAdditionalDemotion;
    }

    /**
     *
     */
    private function fillPromotionUsers()
    {
        $users = $this->findAllUsersWhoPlayLast24Hours();

        $this->usersToPromotion = $this->usersToAdditionalPromotion = [];

        foreach ($users as $user) {
            try {
                $immortalUser = $this->searchImmortalUser($user);
            } catch (UserNotFoundException $e) {
                continue;
            }

            if ($immortalUser->getUsergroupid() == self::REGISTERED_USER_GROUP_ID) {
                $this->usersToPromotion[] = $immortalUser;
            } elseif ($immortalUser->getUsergroupid() == self::SENIOR_GROUP_ID) {
                $this->usersToAdditionalPromotion[] = $immortalUser;
            }
        }
    }

    /**
     *
     */
    private function fillDemotionUsers()
    {
        $users = $this->findAllUsersWhoDontPlayMoreThanMonth();

        $this->usersToDemotion = $this->usersToAdditionalDemotion = [];

        foreach ($users as $user) {
            try {
                $immortalUser = $this->searchImmortalUser($user);
            } catch (UserNotFoundException $e) {
                continue;
            }

            if ($immortalUser->getUsergroupid() == self::PLAYER_GROUP_ID) {
                $this->usersToDemotion[] = $immortalUser;
            } elseif ($immortalUser->getUsergroupid() == self::SENIOR_GROUP_ID) {
                $this->usersToAdditionalDemotion[] = $immortalUser;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getPromotionGroupId(): int
    {
        return self::PLAYER_GROUP_ID;
    }

    /**
     * @inheritDoc
     */
    public function getDemotionGroupId(): int
    {
        return self::REGISTERED_USER_GROUP_ID;
    }

    /**
     * @return array|User[]
     */
    private function findAllUsersWhoDontPlayMoreThanMonth()
    {
        /** @var User[] $users */
        $users = $this->getDefaultManager()
            ->getRepository('CoreBundle:User')
            ->createQueryBuilder('u')
            ->where('u.lastMove BETWEEN :from AND :to')
            ->setParameter('from', new \DateTime('-332day'))
            ->setParameter('to', new \DateTime('-31day'))
            ->getQuery()->getResult();

        return $users;
    }

    /**
     * @return array|User[]
     */
    private function findAllUsersWhoPlayLast24Hours()
    {
        /** @var User[] $users */
        $users = $this->getDefaultManager()
            ->getRepository('CoreBundle:User')
            ->createQueryBuilder('u')
            ->where('u.lastMove BETWEEN :from AND :to')
            ->setParameter('from', new \DateTime('-30day'))
            ->setParameter('to', new \DateTime('now'))
            ->getQuery()->getResult();

        return $users;
    }

    /**
     * @param User $user
     * @return ImmortalUser
     */
    private function searchImmortalUser(User $user) : ImmortalUser
    {
        $repository = $this->getImmortalManager()->getRepository('ImmortalchessNetBundle:ImmortalUser');

        if ($user->getImmortalId()) {
            $immortalUser = $repository->find($user->getImmortalId());
        } else {
            $immortalUser = $repository->findOneByUsername($user->getLogin());
        }

        return $immortalUser;
    }

    /**
     * @return ObjectManager
     */
    private function getImmortalManager() : ObjectManager
    {
        return $this->container->get('doctrine')->getManager('immortalchess');
    }

    /**
     * @return ObjectManager
     */
    private function getDefaultManager() : ObjectManager
    {
        return $this->container->get('doctrine')->getManager();
    }

}