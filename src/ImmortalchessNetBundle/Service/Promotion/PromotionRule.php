<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.17
 * Time: 13:46
 */

namespace ImmortalchessNetBundle\Service\Promotion;

use ImmortalchessNetBundle\Entity\ImmortalUser;

/**
 * Interface PromotionRule
 * @package ImmortalchessNetBundle\Service\Promotion
 */
interface PromotionRule
{
    /**
     * @return array|ImmortalUser[]
     */
    public function getUsersToPromotion();

    /**
     * @return array|ImmortalUser[]
     */
    public function getUsersToDemotion();

    /**
     * @return array|ImmortalUser[]
     */
    public function getUsersToAdditionalPromotion();

    /**
     * @return array|ImmortalUser[]
     */
    public function getUsersToAdditionalDemotion();

    /**
     * @return int
     */
    public function getPromotionGroupId(): int;

    /**
     * @return int
     */
    public function getDemotionGroupId(): int;
}