<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service\Chess;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ChessService
 * @package CoreBundle\Service\Chess
 */
class UciService
{
    use ContainerAwareTrait;
    
    private $resource;

    private $pipes;

    private $thinkingTime = 1;

    /**
     * @return bool
     * @throws \Exception
     */
    private function startGame()
    {
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
     * @param int $wtime
     * @param int $btime
     * @return string
     * @throws \Exception
     */
    public function getBestMoveFromFen(string $fen, int $wtime, int $btime) : string 
    {
        $fenPieces = explode(" ", $fen);
        $moveNumber = $fenPieces[count($fenPieces) - 1];

        $this->container->get("logger")->debug(__METHOD__);

        $this->startGame();

        fwrite($this->pipes[0], "uci\n");
        fwrite($this->pipes[0], "ucinewgame\n");
        fwrite($this->pipes[0], "isready\n");

        if (empty($fen)) {
            fwrite($this->pipes[0], "position startpos\n");
        } else {
            fwrite($this->pipes[0], "position fen $fen\n");
        }

        if ($moveNumber < 10) { // beginning
            $randomSkill = mt_rand(1, 20);
            $moveTime = mt_rand(2000, 5000);
            fwrite($this->pipes[0], "setoption name Skill Level value $randomSkill\n");
            fwrite($this->pipes[0], "go movetime $moveTime\n");
        } else { // middle strength
            fwrite($this->pipes[0], "setoption name Skill Level value 10\n");
            fwrite($this->pipes[0], "setoption name Contempt value 100\n");
            fwrite($this->pipes[0], "go wtime $wtime btime $btime\n");
        }

        while (true) {
            $content = fread($this->pipes[1], 8192);
            
            $this->container->get("logger")->debug("Thinking... " . $content);
            
            preg_match(
                "/bestmove\s(?P<bestmove>[a-h]\d[a-h]\dq?)/i",
                $content,
                $matches
            );

            if (isset($matches["bestmove"])) {
                $this->shutDown();
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