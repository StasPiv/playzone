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
use CoreBundle\Model\Request\Call\CallDeleteDeclineRequest;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallGetRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;

interface CallProcessorInterface
{
    /**
     * @param CallGetRequest $getRequest
     * @param CallGetRequest $getError
     * @return GameCall[]
     */
    public function processGet(CallGetRequest $getRequest, CallGetRequest $getError);

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
     * @param CallDeleteAcceptRequest $acceptRequest
     * @param CallDeleteAcceptRequest $acceptError
     * @return Game
     */
    public function processDeleteAccept(CallDeleteAcceptRequest $acceptRequest, CallDeleteAcceptRequest $acceptError);

    /**
     * @param CallDeleteDeclineRequest $declineRequest
     * @param CallDeleteDeclineRequest $declineError
     * @return GameCall
     */
    public function processDeleteDecline(CallDeleteDeclineRequest $declineRequest, CallDeleteDeclineRequest $declineError);
}