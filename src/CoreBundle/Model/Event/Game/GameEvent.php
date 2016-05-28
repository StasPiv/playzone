<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 13:11
 */

namespace CoreBundle\Model\Event\Game;

use CoreBundle\Model\Event\EventFrequencyAwareTrait;
use CoreBundle\Model\Event\EventInterface;
use CoreBundle\Model\Game\GameContainerAwareTrait;
use CoreBundle\Model\Game\GameContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GameEvent
 * @package CoreBundle\Model\Event\Game
 */
class GameEvent extends Event implements GameContainerInterface, EventInterface
{
    use GameContainerAwareTrait;
    use EventFrequencyAwareTrait;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->getGame()->getUserWhite() . " " . $this->getGame()->getUserBlack();
    }
}