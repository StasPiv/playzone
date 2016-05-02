<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ChessService
 * @package CoreBundle\Service
 */
class ChessService
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    private $pgnDir;

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

    /**
     * @param string $fen
     * @return string
     */
    public function getBestMoveFromFen(string $fen) : string 
    {
        return $this->container->get("core.service.chess.uci")->getBestMoveFromFen($fen);
    }

    /**
     * @return string
     */
    public function getPgnDir() : string
    {
        if (!$this->pgnDir) {
            $this->pgnDir =
                $this->container->get("kernel")->getRootDir() . DIRECTORY_SEPARATOR
                . '..' . DIRECTORY_SEPARATOR .
                'frontend' . DIRECTORY_SEPARATOR . "pgn";
        }

        return $this->pgnDir;
    }

    public function createPgnDir()
    {
        $fs = new Filesystem();

        $pgnDir = $this->container->get("core.service.chess")->getPgnDir();

        if (!$fs->exists($pgnDir)) {
            try {
                $fs->mkdir($pgnDir);
            } catch (IOException $e) {
                $this->container->get("logger")->err($e->getMessage());
            }
        }
    }
}