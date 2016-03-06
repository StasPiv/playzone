<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:58
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Game;
use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;

interface GameProcessorInterface extends ProcessorInterface
{
    /**
     * @param GameGetListRequest $listRequest
     * @param GameGetListRequest $listError
     * @return Game[]
     */
    public function processGetList(GameGetListRequest $listRequest, GameGetListRequest $listError);

    /**
     * @param GameGetRequest $gameRequest
     * @param GameGetRequest $gameError
     * @return Game
     */
    public function processGet(GameGetRequest $gameRequest, GameGetRequest $gameError);

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @param GamePutPgnRequest $pgnError
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $pgnRequest, GamePutPgnRequest $pgnError);

    /**
     * @param GamePutResignRequest $resignRequest
     * @param GamePutResignRequest $resignError
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $resignRequest, GamePutResignRequest $resignError);

    /**
     * @param GamePutOfferdrawRequest $drawRequest
     * @param GamePutOfferdrawRequest $drawError
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $drawRequest, GamePutOfferdrawRequest $drawError);

    /**
     * @param GamePutAcceptdrawRequest $drawRequest
     * @param GamePutAcceptdrawRequest $drawError
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $drawRequest, GamePutAcceptdrawRequest $drawError);
}