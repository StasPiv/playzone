<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.04.16
 * Time: 10:38
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\UserSetting;
use CoreBundle\Exception\Handler\User\UserSettingNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserSettingRepository
 * @package CoreBundle\Repository
 */
class UserSettingRepository extends EntityRepository
{
    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return UserSetting
     * @throws UserSettingNotFoundException
     */
    public function find($id, $lockMode = null, $lockVersion = null) : UserSetting
    {
        if (is_numeric($id)) {
            $userSetting = parent::find($id, $lockMode, $lockVersion);
        } else {
            $userSetting = $this->findOneBy(["code" => $id]);
        }

        if (!$userSetting instanceof UserSetting) {
            throw new UserSettingNotFoundException;
        }
        
        return $userSetting;
    }

}