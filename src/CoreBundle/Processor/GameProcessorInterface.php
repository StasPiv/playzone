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
use CoreBundle\Model\Request\Game\GamePostAddmessageRequest;
use CoreBundle\Model\Request\Game\GamePostNewrobotRequest;
use CoreBundle\Model\Request\Game\GamePostPublishRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutFixRequest;
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
     * @param GamePostNewrobotRequest $request
     * @return Game
     */
    public function processPostNewrobot(GamePostNewrobotRequest $request) : Game;

    /**
     * @param GamePutPgnRequest $request
     * @return Game
     */
    public function processPutPgn(GamePutPgnRequest $request) : Game;

    /**
     * @param GamePostPublishRequest $request
     * @return Game
     */
    public function processPostPublish(GamePostPublishRequest $request) : Game;

    /**
     * @param GamePutResignRequest $request
     * @return Game
     */
    public function processPutResign(GamePutResignRequest $request) : Game;

    /**
     * @param GamePutOfferdrawRequest $request
     * @return Game
     */
    public function processPutOfferdraw(GamePutOfferdrawRequest $request) : Game;

    /**
     * @param GamePutAcceptdrawRequest $request
     * @return Game
     */
    public function processPutAcceptdraw(GamePutAcceptdrawRequest $request) : Game;

    /**
     * @param GamePostAddmessageRequest $request
     * @return Game
     */
    public function processPostAddmessage(GamePostAddmessageRequest $request) : Game;


    /**
     * @param GamePutFixRequest $request
     * @return Game
     */
    public function processPutFix(GamePutFixRequest $request) : Game;
}