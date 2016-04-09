<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:51
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;

/**
 * Interface TournamentProcessorInterface
 * @package CoreBundle\Processor
 */
interface TournamentProcessorInterface
{
    /**
     * @param TournamentGetListRequest $listRequest
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $listRequest) : array;
}