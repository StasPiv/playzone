<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:11
 */

namespace CoreBundle\Processor;


use ApiBundle\Model\Request\Call\CallPostSendRequest;

interface CallProcessorInterface
{
    /**
     * @param CallPostSendRequest $sendRequest
     * @return mixed
     */
    public function processPostSend(CallPostSendRequest $sendRequest);
}