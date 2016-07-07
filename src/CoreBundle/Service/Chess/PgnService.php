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
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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
     * @param array $excludedFens
     * @return PgnGame
     * @throws NotFoundResourceException
     */
    public function getRandomPgnGame(string $pgnPath, array $excludedFens = []) : PgnGame
    {
        $this->parser = new PgnParser($pgnPath);
        $availableGames = [];
        
        foreach ($this->parser->getGames() as $index => $pgnGame) {
            if (!in_array($pgnGame->getFen(), $excludedFens)) {
                $availableGames[] = $pgnGame;
            }
        }
        
        if (empty($availableGames)) {
            throw new NotFoundResourceException;
        }
        
        return $availableGames[mt_rand(0, count($availableGames) - 1)];
    }
}