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
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;

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

    /**
     * @param TournamentPostRecordRequest $listRequest
     * @return Tournament
     */
    public function processPostRecord(TournamentPostRecordRequest $listRequest) : Tournament;
}