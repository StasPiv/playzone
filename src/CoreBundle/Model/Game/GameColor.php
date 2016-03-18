<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:48
 */

namespace CoreBundle\Model\Game;

use MyCLabs\Enum\Enum;

/**
 * Class GameColor
 * @package CoreBundle\Model\Game
 *
 * @method static GameColor WHITE()
 * @method static GameColor BLACK()
 * @method static GameColor RANDOM()
 */
class GameColor extends Enum
{
    const WHITE = "w";
    const BLACK = "b";
    const RANDOM = "random";
}