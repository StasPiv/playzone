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
use CoreBundle\Model\Request\RequestError;

interface CallProcessorInterface
{
    /**
     * @param CallGetRequest $getRequest
     * @param RequestError $getError
     * @return \CoreBundle\Entity\GameCall[]
     */
    public function processGet(CallGetRequest $getRequest, RequestError $getError);

    /**
     * @param CallPostSendRequest $sendRequest
     * @param RequestError $sendError
     * @return \CoreBundle\Entity\GameCall[]
     */
    public function processPostSend(CallPostSendRequest $sendRequest, RequestError $sendError);

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @param RequestError $removeError
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest, RequestError $removeError);

    /**
     * @param CallDeleteAcceptRequest $acceptRequest
     * @param RequestError $acceptError
     * @return Game
     */
    public function processDeleteAccept(CallDeleteAcceptRequest $acceptRequest, RequestError $acceptError);

    /**
     * @param CallDeleteDeclineRequest $declineRequest
     * @param RequestError $declineError
     * @return GameCall
     */
    public function processDeleteDecline(CallDeleteDeclineRequest $declineRequest, RequestError $declineError);
}