<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.06.16
 * Time: 23:11
 */
namespace CoreBundle\Model\Request\Game;


/**
 * Class GamePutPgnRequest
 * @package CoreBundle\Model\Request\Game
 */
interface GameFixResultInterface
{
    /**
     * @return int
     */
    public function getTimeWhite();

    /**
     * @return int
     */
    public function getTimeBlack();

    /**
     * @return boolean
     */
    public function isInsufficientMaterialWhite() : bool;

    /**
     * @return boolean
     */
    public function isInsufficientMaterialBlack() : bool;
}