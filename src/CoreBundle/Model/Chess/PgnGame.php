<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 22:34
 */

namespace CoreBundle\Model\Chess;

use AmyBoyd\PgnParser\Game;

/**
 * Class PgnGame
 * @package CoreBundle\Model\Chess
 */
class PgnGame extends Game
{
    /** @var string */
    private $fen;

    /**
     * @return string
     */
    public function getFen() : string
    {
        return $this->fen;
    }

    /**
     * @param string $fen
     * @return PgnGame
     */
    public function setFen(string $fen) : self
    {
        $this->fen = $fen;

        return $this;
    }
}