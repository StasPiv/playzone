<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:07
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\ChatMessage;
use CoreBundle\Model\Request\Chat\ChatGetMessagesRequest;
use CoreBundle\Model\Request\Chat\ChatPostMessageRequest;

/**
 * Interface ChatProcessorInterface
 * @package CoreBundle\Processor
 */
interface ChatProcessorInterface
{
    /**
     * @param ChatPostMessageRequest $request
     * @return mixed
     */
    public function processPostMessage(ChatPostMessageRequest $request) : ChatMessage;

    /**
     * @param ChatGetMessagesRequest $request
     * @return ChatMessage[]
     */
    public function processGetMessages(ChatGetMessagesRequest $request) : array;
}