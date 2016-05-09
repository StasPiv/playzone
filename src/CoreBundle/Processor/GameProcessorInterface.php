<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:58
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Game;
use CoreBundle\Model\Game\GameMove;
use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GameGetRobotmoveAction;
use CoreBundle\Model\Request\Game\GamePostAddmessageRequest;
use CoreBundle\Model\Request\Game\GamePostNewrobotRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;
use CoreBundle\Model\Request\RequestErrorInterface;

interface GameProcessorInterface extends ProcessorInterface
{
    /**
     * @param GameGetListRequest $listRequest
     * @return array|\CoreBundle\Entity\Game[]
     */
    public function processGetList(GameGetListRequest $listRequest) : array;

    /**
     * @param GameGetRequest $gameRequest
     * @return Game
     */
    public function processGet(GameGetRequest $gameRequest) : Game;

    /**
     * @param GameGetRobotmoveAction $request
     * @return GameMove
     */
    public function processGetRobotmove(GameGetRobotmoveAction $request) : GameMove;

    /**
     * @param GamePostNewrobotRequest $request
     * @return Game
     */
    public function processPostNewrobot(GamePostNewrobotRequest $request) : Game;

    /**
     * @param GamePutPgnRequest $pgnRequest
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $pgnRequest) : Game;

    /**
     * @param GamePutResignRequest $resignRequest
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $resignRequest) : Game;

    /**
     * @param GamePutOfferdrawRequest $drawRequest
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $drawRequest) : Game;

    /**
     * @param GamePutAcceptdrawRequest $drawRequest
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $drawRequest) : Game;

    /**
     * @param GamePostAddmessageRequest $request
     * @return Game
     */
    public function processPostAddmessage(GamePostAddmessageRequest $request) : Game;
}