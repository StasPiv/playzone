<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.04.17
 * Time: 11:30
 */

namespace CoreBundle\Service\Chess\PgnService;

use function Couchbase\defaultDecoder;

/**
 * Class GetGameFactory
 * @package CoreBundle\Service\Chess\PgnService
 */
class GetGameFactory
{
    /**
     * @var GetRandomGame
     */
    private $randomGame;

    /**
     * @inheritDoc
     * @param GetRandomGame $randomGame
     */
    public function __construct(GetRandomGame $randomGame)
    {
        $this->randomGame = $randomGame;
    }


    /**
     * @param string $strategy
     * @return GetGameInterface
     * @internal param string $pgnPath
     * @internal param array $params
     */
    public function create(string $strategy): GetGameInterface
    {
        switch ($strategy) {
            case 'random':
                return $this->randomGame;
            case 'by_order':
                return;
            default:
                throw new \RuntimeException(sprintf('Unknown getGame strategy: %s', $strategy));
        }
    }
}