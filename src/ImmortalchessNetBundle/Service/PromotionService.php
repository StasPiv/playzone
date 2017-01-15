<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.17
 * Time: 12:01
 */

namespace ImmortalchessNetBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use ImmortalchessNetBundle\Service\Promotion\PromotionRule;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PromotionService
 * @package ImmortalchessNetBundle\Service
 */
class PromotionService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    const REGISTERED_USER_GROUP_ID = 2;

    const PLAYER_GROUP_ID = 23;

    /**
     * @param PromotionRule $promotionRule
     * @param OutputInterface $output
     */
    public function run(PromotionRule $promotionRule, OutputInterface $output)
    {
        foreach ($promotionRule->getUsersToDemotion() as $user) {
            $user->setUsergroupid($promotionRule->getDemotionGroupId());
            $output->writeln('Demote: '.$user->getUsername());
            $this->getManager()->persist($user);
        }

        foreach ($promotionRule->getUsersToPromotion() as $user) {
            $user->setUsergroupid($promotionRule->getPromotionGroupId());
            $output->writeln('Promote: '.$user->getUsername());
            $this->getManager()->persist($user);
        }

        foreach ($promotionRule->getUsersToAdditionalDemotion() as $user) {
            $additionalGroupIds = $user->getMembergroupidsAsArray();

            if(($key = array_search($promotionRule->getPromotionGroupId(), $additionalGroupIds)) !== false) {
                unset($additionalGroupIds[$key]);
            }

            $user->setMembergroupids(empty($additionalGroupIds) ? '' : implode(',', $additionalGroupIds));

            $output->writeln('Additional demote: '.$user->getUsername());

            $this->getManager()->persist($user);
        }

        foreach ($promotionRule->getUsersToAdditionalPromotion() as $user) {
            $additionalGroupIds = $user->getMembergroupidsAsArray();

            if(($key = array_search($promotionRule->getPromotionGroupId(), $additionalGroupIds)) === false) {
                $additionalGroupIds[] = $promotionRule->getPromotionGroupId();
            }

            $user->setMembergroupids(implode(',', $additionalGroupIds));

            $output->writeln('Additional promote: '.$user->getUsername());

            $this->getManager()->persist($user);
        }

        $this->getManager()->flush();
    }

    /**
     * @return ObjectManager
     */
    private function getManager() : ObjectManager
    {
        return $this->container->get('doctrine')->getManager('immortalchess');
    }
}