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
use CoreBundle\Model\Request\RequestError;

interface GameProcessorInterface extends ProcessorInterface
{
    /**
     * @param GameGetListRequest $listRequest
     * @param RequestError $listError
     * @return \CoreBundle\Entity\Game[]
     */
    public function processGetList(GameGetListRequest $listRequest, RequestError $listError);

    /**
     * @param GameGetRequest $gameRequest
     * @param RequestError $gameError
     * @return Game
     */
    public function processGet(GameGetRequest $gameRequest, RequestError $gameError);

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @param RequestError $pgnError
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $pgnRequest, RequestError $pgnError);

    /**
     * @param GamePutResignRequest $resignRequest
     * @param RequestError $resignError
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $resignRequest, RequestError $resignError);

    /**
     * @param GamePutOfferdrawRequest $drawRequest
     * @param RequestError $drawError
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $drawRequest, RequestError $drawError);

    /**
     * @param GamePutAcceptdrawRequest $drawRequest
     * @param RequestError $drawError
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $drawRequest, RequestError $drawError);
}