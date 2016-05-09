<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.05.16
 * Time: 18:12
 */

namespace CoreBundle\Model\Tournament\Params;


use CoreBundle\Model\Tournament\TournamentParams;
use CoreBundle\Model\Tournament\TournamentType;

/**
 * Class TournamentRoundrobinParams
 * @package CoreBundle\Model\Tournament\Params
 */
class TournamentRoundrobinParams extends TournamentParams
{
    /**
     * @return TournamentType
     */
    public function getType() : TournamentType
    {
        return TournamentType::ROUND_ROBIN();
    }
}