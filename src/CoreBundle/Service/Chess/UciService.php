<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service\Chess;

/**
 * Class ChessService
 * @package CoreBundle\Service\Chess
 */
class UciService
{
    private $resource;

    private $pipes;

    private $thinkingTime = 1;

    /**
     * @return bool
     * @throws \Exception
     */
    private function startGame()
    {
        if (!$this->resource) {
            $this->resource = proc_open(
                '/usr/games/stockfish',
                [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["file", "/tmp/uci_err", "w+"]
                ],
                $this->pipes,
                '/tmp',
                []
            );
        }

        if (!is_resource($this->resource)) {
            $this->shutDown();

            throw new \Exception("Resource unavailable !");
        } else {
            $this->thinkingTime = mt_rand(50, 200) / 100;
            return true;
        }
    }

    /**
     * @param string $fen
     * @return string
     */
    public function getBestMoveFromFen(string $fen) : string 
    {
        $this->startGame();

        fwrite($this->pipes[0], "uci\n");
        fwrite($this->pipes[0], "ucinewgame\n");
        fwrite($this->pipes[0], "isready\n");

        if (empty($fen)) {
            fwrite($this->pipes[0], "position startpos\n");
        } else {
            fwrite($this->pipes[0], "position startpos fen $fen\n");
        }

        fwrite($this->pipes[0], "go\n");

        $start = microtime(true);

        echo $this->thinkingTime;
        while (true) {
            if (microtime(true) - $start > $this->thinkingTime) {
                fclose($this->pipes[0]);
                preg_match(
                    "/bestmove\s(?P<bestmove>[a-h]\d[a-h]\dq?)/i",
                    stream_get_contents($this->pipes[1]),
                    $matches
                );
                return $matches["bestmove"];
            }
        }

        return "";
    }

    public function __destruct()
    {
        $this->shutDown();
    }

    protected function shutDown()
    {
        @fclose($this->pipes[0]);
        @fclose($this->pipes[1]);
        @fclose($this->pipes[2]);
        @fclose($this->resource);
    }
}