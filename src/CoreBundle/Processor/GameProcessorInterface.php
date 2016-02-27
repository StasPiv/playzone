<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:58
 */

namespace CoreBundle\Processor;

use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePostPgnRequest;

interface GameProcessorInterface extends ProcessorInterface
{
    /**
     * @param GameGetListRequest $listRequest
     * @param GameGetListRequest $listError
     * @return \CoreBundle\Entity\Game[]
     */
    public function processGetList(GameGetListRequest $listRequest, GameGetListRequest $listError);

    /**
     * @param GameGetRequest $gameRequest
     * @param GameGetRequest $gameError
     * @return \CoreBundle\Entity\Game
     */
    public function processGet(GameGetRequest $gameRequest, GameGetRequest $gameError);

    /**
     * @param GamePostPgnRequest $pgnRequest
     * @param GamePostPgnRequest $pgnError
     * @return \CoreBundle\Entity\Game
     */
    public function processPostPgn(GamePostPgnRequest $pgnRequest, GamePostPgnRequest $pgnError);
}