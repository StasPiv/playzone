<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 15:27
 */

namespace CoreBundle\Model\Tournament;

use MyCLabs\Enum\Enum;

/**
 * Class TournamentType
 * @package CoreBundle\Model\Tournament
 * 
 * @method static TournamentType SWITZ()
 */
class TournamentType extends Enum
{
    const SWITZ = 'switz';
}