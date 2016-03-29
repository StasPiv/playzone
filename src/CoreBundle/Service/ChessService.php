<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service;


use CoreBundle\Entity\Game;

class ChessService
{
    /**
     * @param string $pgn
     * @return bool
     */
    public function isValidPgn($pgn)
    {
        return strpos($pgn, '1.') !== false;
    }

    /**
     * @param string $pgnEncoded
     * @return string pgn
     */
    public function decodePgn($pgnEncoded)
    {
        return base64_decode($pgnEncoded);
    }

    /**
     * @param string $pgn
     * @return bool
     */
    public function isGameInCheckmate(string $pgn) : bool
    {
        return substr($pgn, -1, 1) === '#';
    }
}