<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 13:11
 */

namespace CoreBundle\Model\Event\Game;

use CoreBundle\Model\Game\GameContainerAwareTrait;
use CoreBundle\Model\Game\GameContainerInterface;
use CoreBundle\Model\User\UserContainerAwareTrait;
use CoreBundle\Model\User\UserContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GameEvent
 * @package CoreBundle\Model\Event\Game
 */
class GamePublishEvent extends Event implements GameContainerInterface, UserContainerInterface
{
    use GameContainerAwareTrait;
    use UserContainerAwareTrait;
    
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $pgn;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $fen;

    /**
     * @return string
     */
    public function getPgn()
    {
        return $this->pgn;
    }

    /**
     * @param string $pgn
     * @return GamePublishEvent
     */
    public function setPgn($pgn)
    {
        $this->pgn = $pgn;

        return $this;
    }

    /**
     * @return string
     */
    public function getFen()
    {
        return $this->fen;
    }

    /**
     * @param string $fen
     * @return GamePublishEvent
     */
    public function setFen($fen)
    {
        $this->fen = $fen;

        return $this;
    }
}