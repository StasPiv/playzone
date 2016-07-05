<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 23:41
 */

namespace CoreBundle\Model\Event\Game;

use CoreBundle\Model\Event\EventFrequencyAwareTrait;
use CoreBundle\Model\Event\EventInterface;

/**
 * Class SimpleEvent
 * @package CoreBundle\Model\Event\Game
 */
class SimpleEvent implements EventInterface
{
    use EventFrequencyAwareTrait;
    
    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return "Simple event";
    }
}