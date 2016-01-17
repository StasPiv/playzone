<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:11
 */

namespace CoreBundle\Processor;


use CoreBundle\Model\Request\Call\CallPostSendRequest;

interface CallProcessorInterface
{
    /**
     * @param CallPostSendRequest $sendRequest
     * @param CallPostSendRequest $sendError
     * @return mixed
     */
    public function processPostSend(CallPostSendRequest $sendRequest, CallPostSendRequest $sendError);
}