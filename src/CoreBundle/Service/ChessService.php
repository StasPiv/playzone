<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Service\Chess\ChessGameService;
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

    /** @var ChessGameService */
    private $chessGameService;

    /**
     * ChessService constructor.
     */
    public function __construct()
    {
        $this->chessGameService = new ChessGameService();
    }

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
     * @param Game $game
     * @return bool
     */
    public function fixResult(Game $game) : bool
    {
        $this->chessGameService->setPgn($game->getPgn());
        
        switch ($this->chessGameService->gameOver()) {
            case 'W':
                $game->setResultWhite(1)->setResultBlack(0);
                return true;
            case 'B':
                $game->setResultWhite(0)->setResultBlack(1);
                return true;
            case 'D':
                $game->setResultWhite(0.5)->setResultBlack(0.5);
                return true;
            default:
                return false;
        }
    }

    /**
     * @param Game $game
     * @return bool
     */
    public function canAbort(Game $game): bool
    {
        $this->chessGameService->setPgn($game->getPgn());

        return count($this->chessGameService->getMoveList()) < 3;
    }

    /**
     * @param string $pgn for example 1. f4 d5 2. g4 e5 3. b4 Qh4#
     * @return string GameColor constant
     */
    public function defineColorToMoveByPgn(string $pgn) : string 
    {
        $chunks = explode(" ", $pgn);
        return strpos($chunks[count($chunks) - 2], '.') === false ?
            GameColor::WHITE : GameColor::BLACK;
    }

    /**
     * @param string $pgn
     * @return bool
     */
    private function isGameOver(string $pgn) : bool
    {
        return $this->isGameInCheckmate($pgn);
    }

    /**
     * @param string $pgn
     * @return bool
     */
    private function isGameInCheckmate(string $pgn) : bool
    {
        return substr($pgn, -1, 1) === '#';
    }

    /**
     * @param string $fen
     * @param int $wtime
     * @param int $btime
     * @param int $skillLevel
     * @param string $engine
     * @return string
     */
    public function getBestMoveFromFen(
        string $fen,
        int $wtime,
        int $btime,
        int $skillLevel = 20,
        string $engine = 'stockfish'
    ) : string 
    {
        return $this->container->get("core.service.chess.uci")->getBestMoveFromFen($fen, $wtime, $btime, $skillLevel, $engine);
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