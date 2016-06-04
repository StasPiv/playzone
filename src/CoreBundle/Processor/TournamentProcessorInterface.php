<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:51
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;

/**
 * Interface TournamentProcessorInterface
 * @package CoreBundle\Processor
 */
interface TournamentProcessorInterface
{
    /**
     * @param TournamentGetListRequest $request
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $request) : array;

    /**
     * @param TournamentGetRequest $request
     * @return Tournament
     */
    public function processGet(TournamentGetRequest $request) : Tournament;

    /**
     * @param TournamentGetCurrentgameRequest $request
     * @return Game
     */
    public function processGetCurrentgame(TournamentGetCurrentgameRequest $request) : Game;

    /**
     * @param TournamentPostRecordRequest $request
     * @return Tournament
     */
    public function processPostRecord(TournamentPostRecordRequest $request) : Tournament;

    /**
     * @param TournamentDeleteUnrecordRequest $unrecordRequest
     * @return Tournament
     * @internal param TournamentDeleteUnrecordRequest $listRequest
     */
    public function processDeleteUnrecord(TournamentDeleteUnrecordRequest $unrecordRequest) : Tournament;
}