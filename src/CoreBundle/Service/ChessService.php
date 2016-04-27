<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 20:44
 */

namespace CoreBundle\Service;

use Symfony\Component\Process\Process;

class ChessService
{
    const UCI_MAX_THINK_TIME = 1;

    private $resorce;

    private $pipes;

    private $skill	= 10;

    private static $instance;

    /**
     *
     * @return UCI
     */
    public static function get()
    {
        if ( ! self::$instance)
        {
            self::$instance = new UCI();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->resorce	= null;

        $this->startGame();
    }

    protected function startGame()
    {
        if ( ! $this->resorce)
        {
            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//			   1 => array("file", "/tmp/engine_log", 'w+'),  // stdout is a pipe that the child will write to
                2 => array("file", "/tmp/uci_err", "w+") // stderr is a file to write to
            );
            $cwd = '/tmp';

            $env = array();
            $this->resorce = proc_open('/usr/games/stockfish', $descriptorspec, $this->pipes, $cwd, $env);
        }

        if ( ! is_resource($this->resorce))
        {
            $this->shutDown();

            throw new \Exception("Resource unavailable !");
        }
        else
        {
            return true;
        }
    }

    public function setSkillLevel($level)
    {
        $this->skill	= (int) $level;
    }

    /**
     *
     * @param array $moves Algebraic notation moves
     * @param array Assoc. array of additional properties to pass to "go" function
     *	(e.g. wtime => 50000)
     */
    public function getBestMove(Array $moves, Array $properties = array())
    {
        $moves	= implode(" ", $moves);

        if ( ! is_resource($this->resorce))
        {
            $this->startGame();
        }

        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt
        fwrite($this->pipes[0], "uci\n");
        fwrite($this->pipes[0], "ucinewgame\n");
        fwrite($this->pipes[0], "isready\n");
        usleep(500);

        if (empty($moves))
        {
            fwrite($this->pipes[0], "position startpos\n");
        }
        else
        {
            fwrite($this->pipes[0], "position startpos moves $moves\n");
        }

        $go_modifiers = "";

        if ( ! empty($properties))
        {
            foreach ($properties AS $name => $value)
            {
                $go_modifiers .= "$name $value ";
            }
        }

        if ( ! isset($properties['movetime']))
        {
            $go_modifiers	.= "movetime 3000 ";
        }

        fwrite($this->pipes[0], "go $go_modifiers\n");

        $start = microtime(true);

        $matches = [];

        while (true) {
            if (microtime(true) - $start > self::UCI_MAX_THINK_TIME) {
                fclose($this->pipes[0]);
                $return = stream_get_contents($this->pipes[1]);
                preg_match("/bestmove\s(?P<bestmove>[a-h]\d[a-h]\dq?)/i", $return, $matches);
                break;
            }
        }

        return $matches["bestmove"];
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
        @fclose($this->resorce);
    }
}