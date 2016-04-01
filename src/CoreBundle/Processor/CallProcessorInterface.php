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
use CoreBundle\Model\Request\RequestErrorInterface;

interface CallProcessorInterface
{
    /**
     * @param CallGetRequest $getRequest
     * @return array|\CoreBundle\Entity\GameCall[]
     */
    public function processGet(CallGetRequest $getRequest) : array;

    /**
     * @param CallPostSendRequest $sendRequest
     * @return GameCall
     */
    public function processPostSend(CallPostSendRequest $sendRequest) : GameCall;

    /**
     * @param CallDeleteRemoveRequest $removeRequest
     * @return GameCall
     */
    public function processDeleteRemove(CallDeleteRemoveRequest $removeRequest) : GameCall;

    /**
     * @param CallDeleteAcceptRequest $acceptRequest
     * @return Game
     */
    public function processDeleteAccept(CallDeleteAcceptRequest $acceptRequest) : Game;

    /**
     * @param CallDeleteDeclineRequest $declineRequest
     * @return GameCall
     */
    public function processDeleteDecline(CallDeleteDeclineRequest $declineRequest) : GameCall;
}