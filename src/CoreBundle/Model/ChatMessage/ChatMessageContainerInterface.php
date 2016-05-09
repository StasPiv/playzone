<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 17:44
 */

namespace CoreBundle\Model\ChatMessage;

use CoreBundle\Entity\ChatMessage;

/**
 * Class ChatContainerInterface
 * @package CoreBundle\Model\ChatMessage
 */
interface ChatMessageContainerInterface
{
    /**
     * @param ChatMessage $chatMessage
     * @return mixed
     */
    function addChatMessage(ChatMessage $chatMessage);

    /**
     * @return ChatMessage[]
     */
    function getChatMessages() : array;
}