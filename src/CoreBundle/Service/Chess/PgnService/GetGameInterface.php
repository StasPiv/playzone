<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.04.17
 * Time: 11:29
 */

namespace CoreBundle\Service\Chess\PgnService;

/**
 * Interface GetGameStrategyInterface
 * @package CoreBundle\Service\Chess\PgnService
 */
interface GetGameInterface
{
    /**
     * @param string $pgnPath
     * @return mixed
     * @internal param array $params
     * @internal param string $pgnPath
     * @internal param array $params
     */
    public function getGame(string $pgnPath);
}