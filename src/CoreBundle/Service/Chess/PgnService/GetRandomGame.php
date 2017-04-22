<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.04.17
 * Time: 11:34
 */

namespace CoreBundle\Service\Chess\PgnService;

use CoreBundle\Service\Chess\Pgn\PgnParser;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class GetRandomGame
 * @package CoreBundle\Service\Chess\PgnService
 */
class GetRandomGame implements GetGameInterface
{
    /**
     * @var array
     */
    private $params = [];
    /**
     * @var string
     */
    private $pgnPath;

    /**
     * @inheritDoc
     * @param string $params
     * @param array $params
     */
    public function __construct(string $pgnPath, array $params)
    {
        if (!isset($params['excluded_fens'])) {
            throw new \RuntimeException('excluded_fens param is required for GetRandomGame');
        }

        $this->pgnPath = $params;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function getGame()
    {
        $pgnParser = new PgnParser($this->pgnPath);
        $availableGames = [];

        foreach ($pgnParser->getGames() as $index => $pgnGame) {
            if (!in_array($pgnGame->getFen(), $this->params['excluded_fens'])) {
                $availableGames[] = $pgnGame;
            }
        }

        if (empty($availableGames)) {
            throw new NotFoundResourceException;
        }

        return $availableGames[mt_rand(0, count($availableGames) - 1)];
    }

}