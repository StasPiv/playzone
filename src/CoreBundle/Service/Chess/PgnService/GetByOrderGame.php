<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.04.17
 * Time: 12:08
 */

namespace CoreBundle\Service\Chess\PgnService;


use CoreBundle\Service\Chess\Pgn\PgnParser;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class GetByOrderGame
 * @package CoreBundle\Service\Chess\PgnService
 */
class GetByOrderGame implements GetGameInterface
{
    /**
     * @inheritDoc
     */
    public function getGame(string $pgnPath)
    {
        $pgnParser = new PgnParser($pgnPath);

        $availableGames = [];

        foreach ($pgnParser->getGames() as $index => $pgnGame) {

        }

        if (empty($availableGames)) {
            throw new NotFoundResourceException;
        }

        return $availableGames[mt_rand(0, count($availableGames) - 1)];
    }

}