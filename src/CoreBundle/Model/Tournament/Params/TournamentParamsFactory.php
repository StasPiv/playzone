<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 15:30
 */

namespace CoreBundle\Model\Tournament\Params;

use CoreBundle\Exception\Handler\Tournament\TournamentParamsNotFound;
use CoreBundle\Exception\Handler\TournamentHandlerException;
use CoreBundle\Model\Tournament\TournamentParams;

/**
 * Class TournamentParamsFactory
 * @package CoreBundle\Model\Tournament\Params
 */
final class TournamentParamsFactory
{
    /**
     * @param string $type
     * @return TournamentParams
     */
    static public function create(string $type) : TournamentParams
    {
        switch ($type) {
            case 'switz':
                return new TournamentSwitzParams();
            case 'round_robin':
                return new TournamentRoundrobinParams();
            default:
                throw new TournamentParamsNotFound;
        }
    }
}