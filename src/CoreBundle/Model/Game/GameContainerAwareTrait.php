<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 13:13
 */

namespace CoreBundle\Model\Game;

use CoreBundle\Entity\Game;

/**
 * Class GameContainerAwareTrait
 * @package CoreBundle\Model\Game
 */
trait GameContainerAwareTrait
{
    /**
     * @var Game
     */
    protected $game;

    /**
     * @return Game
     */
    public function getGame() : Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     * @return GameContainerAwareTrait
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }
}