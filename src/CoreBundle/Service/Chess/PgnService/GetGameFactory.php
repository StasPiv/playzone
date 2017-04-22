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
     * @param string $strategy
     * @param string $pgnPath
     * @param array $params
     * @return GetGameInterface
     */
    public static function create(string $strategy, string $pgnPath, array $params): GetGameInterface
    {
        switch ($strategy) {
            case 'random':
                return new GetRandomGame($pgnPath, $params);
            case 'by_order':
                return;
            default:
                throw new \RuntimeException(sprintf('Unknown getGame strategy: %s', $strategy));
        }
    }
}