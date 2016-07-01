<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.06.16
 * Time: 10:30
 */

namespace CoreBundle\Service\Chess;

use Chess\Game\ChessGame;

/**
 * Class ChessGameService
 * @package CoreBundle\Service\Chess
 */
class ChessGameService extends ChessGame
{
    /**
     * @param string $pgn
     */
    public function setPgn(string $pgn)
    {
        $moves = explode(" ", $pgn);
        foreach ($moves as $move) {
            $finalMove = preg_replace('/^\d+\./', '', $move);
            $finalMove = preg_replace('/[#+]/', '', $finalMove);
            $this->moveSAN($finalMove);
        }
    }

    /**
     * @return array
     */
    public function getPgn()
    {
        return $this->getMoveListString();
    }

    /**
     * @inheritDoc
     */
    public function gameOver()
    {
        if ($this->inClaimableDraw()) {
            return 'D';
        }
        
        return parent::gameOver();
    }
}