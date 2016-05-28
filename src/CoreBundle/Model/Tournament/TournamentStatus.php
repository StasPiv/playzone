<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 17:29
 */

namespace CoreBundle\Model\Tournament;

use MyCLabs\Enum\Enum;

/**
 * Class TournamentStatus
 * @package CoreBundle\Model\Tournament
 *
 * @method static TournamentStatus NEW()
 * @method static TournamentStatus CURRENT()
 * @method static TournamentStatus END()
 */
class TournamentStatus extends Enum
{
    const NEW = 'new';
    const CURRENT = 'current';
    const END = 'end';
}