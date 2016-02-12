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
     * @return mixed
     */
    public function processGet(GameGetRequest $gameRequest, GameGetRequest $gameError);
}