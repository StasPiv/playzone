<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:11
 */

namespace CoreBundle\Processor;


use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallPutAcceptRequest;

interface CallProcessorInterface
{
    /**
     * @param CallPostSendRequest $sendRequest
     * @param CallPostSendRequest $sendError
     * @return GameCall[]
     */
    public function processPostSend(CallPostSendRequest $sendRequest, CallPostSendRequest $sendError);

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @param CallDeleteRemoveRequest $removeError
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest, CallDeleteRemoveRequest $removeError);

    /**
     * @param CallPutAcceptRequest $acceptRequest
     * @param CallPutAcceptRequest $acceptError
     * @return Game
     */
    public function processPutAccept(CallPutAcceptRequest $acceptRequest, CallPutAcceptRequest $acceptError);
}