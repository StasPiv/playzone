<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.06.16
 * Time: 10:52
 */

namespace CoreBundle\Service\Tournament\TournamentTable;

use CoreBundle\Exception\Handler\Tournament\TournamentIncorrectTypeException;
use CoreBundle\Model\Tournament\TournamentType;
use CoreBundle\Service\Tournament\TournamentTableInterface;

/**
 * Class TournamentTableFactory
 * @package CoreBundle\Service\Tournament\TournamentTable
 */
class TournamentTableFactory
{
    /**
     * @param TournamentType $type
     * @return TournamentTableInterface
     */
    public function create(TournamentType $type)
    {
        switch ($type) {
            case TournamentType::ROUND_ROBIN():
                return new TournamentTableRoundRobin();
            case TournamentType::SWITZ():
                return new TournamentTableSwitz();
            default:
                throw new TournamentIncorrectTypeException;
        }
    }
}