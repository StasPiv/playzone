<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 31.05.16
 * Time: 12:21
 */

namespace CoreBundle\Service;

use Behat\Transliterator\Transliterator;
use CoreBundle\Entity\UserSetting;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UserSettingService
 * @package CoreBundle\Service
 */
class UserSettingService
{
    use ContainerAwareTrait;
    
    public function updateCodes()
    {
        $settings = $this->container->get("doctrine")
            ->getRepository("CoreBundle:UserSetting")->findAll();

        $manager = $this->container->get("doctrine")->getManager();
        
        foreach ($settings as $setting) {
            $setting->setCode(Transliterator::transliterate($setting->getName()));
            $manager->persist($setting);
        }
        
        $manager->flush();
    }
}