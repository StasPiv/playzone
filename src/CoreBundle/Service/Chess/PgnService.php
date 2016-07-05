<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 22:32
 */

namespace CoreBundle\Service\Chess;

use CoreBundle\Service\Chess\Pgn\PgnParser;
use CoreBundle\Model\Chess\PgnGame;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PgnService
 * @package CoreBundle\Service\Chess
 */
class PgnService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var PgnParser */
    private $parser;

    /**
     * @param string $pgnPath
     * @return PgnGame
     */
    public function getRandomPgn(string $pgnPath) : PgnGame
    {
        $this->parser = new PgnParser($pgnPath);
        return $this->getPgnGameByIndex(
            $pgnPath, mt_rand(0, count($this->parser->getGames()) - 1)
        );
    }

    /**
     * @param string $pgnPath
     * @param int $index
     * @return PgnGame
     */
    public function getPgnGameByIndex(string $pgnPath, int $index)
    {
        if (!file_exists($pgnPath)) {
            throw new FileNotFoundException;
        }

        $content = file_get_contents($pgnPath);

        if (empty($content)) {
            throw new FileNotFoundException;
        }


        return $this->parser->getGame($index);
    }
}