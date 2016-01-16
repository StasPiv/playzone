<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:58
 */

namespace CoreBundle\Processor;


use ApiBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Entity\Game;

interface GameProcessorInterface extends ProcessorInterface
{
    /**
     * @param GameGetListRequest $listRequest
     * @return Game[]
     */
    public function processGetList(GameGetListRequest $listRequest);
}