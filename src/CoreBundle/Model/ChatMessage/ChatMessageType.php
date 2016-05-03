<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 17:50
 */

namespace CoreBundle\Model\ChatMessage;

use MyCLabs\Enum\Enum;

/**
 * Class ChatMessageType
 * @package CoreBundle\Model\ChatMessage
 *
 * @method static GAME()
 * @method static COMMON()
 */
class ChatMessageType extends Enum
{
    const GAME = "game";
    const COMMON = "common";
}