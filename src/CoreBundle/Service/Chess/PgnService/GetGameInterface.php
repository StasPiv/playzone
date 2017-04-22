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
     * GetGameInterface constructor.
     * @param string $pgnPath
     * @param array $params
     */
    public function __construct(string $pgnPath, array $params);

    /**
     * @return mixed
     * @internal param string $pgnPath
     * @internal param array $params
     */
    public function getGame();
}