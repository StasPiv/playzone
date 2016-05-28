<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 13:12
 */

namespace CoreBundle\Model\Game;

use CoreBundle\Entity\Game;

/**
 * Interface GameContainerInterface
 * @package CoreBundle\Model\Game
 */
interface GameContainerInterface
{
    /**
     * @return Game
     */
    public function getGame() : Game;
}