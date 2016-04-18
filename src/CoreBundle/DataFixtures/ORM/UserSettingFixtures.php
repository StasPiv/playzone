<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.04.16
 * Time: 10:48
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\UserSetting;

/**
 * Class SettingFixtures
 * @package CoreBundle\DataFixtures\ORM
 */
class UserSettingFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $setting = new UserSetting();
        
        $setting->setName($data["name"])
                ->setType($data["type"]);
        
        return $setting;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10;
    }

}