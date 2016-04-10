<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:48
 */

namespace CoreBundle\Model\Game;

use JMS\Serializer\Annotation as JMS;

/**
 * Class GameColor
 * @package CoreBundle\Model\Game
 */
class GameColor
{
    const WHITE = "w";
    const BLACK = "b";
    const RANDOM = "random";

    /**
     * @param string $color
     * @return string
     */
    public static function getOppositeColor(string $color) : string 
    {
        return $color == self::WHITE ? self::BLACK : self::WHITE;
    }
}