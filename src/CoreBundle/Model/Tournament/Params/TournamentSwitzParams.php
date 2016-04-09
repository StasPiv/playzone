<?php

namespace CoreBundle\Model\Tournament\Params;

use CoreBundle\Model\Tournament\TournamentParams;
use CoreBundle\Model\Tournament\TournamentType;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 15:25
 */
class TournamentSwitzParams extends TournamentParams
{
    /**
     * @return TournamentType
     */
    public function getType() : TournamentType
    {
        return TournamentType::SWITZ();
    }

}