<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 12:04
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\User;
use CoreBundle\Entity\UserSetting;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;

class UserFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * @inheritDoc
     */
    protected function createEntity($data)
    {
        $user = new User();
        $user->setLogin($data['login'])
            ->setEmail($data['email'])
            ->setPassword(md5($data['password']))
            ->setHash(md5($data['hash']))
            ->setConfirm($data['confirm'])
            ->setInRest($data['in_rest'])
            ->setLeftRest($data['left_rest'])
            ->setGoneInRest(new \DateTime($data['gone_in_rest']))
            ->setLastAuth(new \DateTime($data['last_auth']))
            ->setClass($data['class'])
            ->setRating($data['rating'])
            ->setWin($data['win'])
            ->setDraw($data['draw'])
            ->setLose($data['lose'])
            ->setLoseTime($data['lose_time'])
            ->setImmortalId($data['immortal_id'])
            ->setAnotherLogin($data['another_login'])
            ->setLastMove(new \DateTime($data['last_move']))
            ->setBalance($data['balance']);

        if (isset($data["settings"])) {
            foreach ($data["settings"] as $settingArray) {
                /** @var UserSetting $userSetting */
                $userSetting = $this->getReference($settingArray["settingReference"]);
                $userSetting->setValue($settingArray["value"]);

                $user->setSetting($userSetting);
            }
        }

        return $user;
    }
}