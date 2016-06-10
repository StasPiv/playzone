<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.06.16
 * Time: 0:15
 */

namespace CoreBundle\Tests\Handler\Game;

use CoreBundle\Entity\Game;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Service\Game\GameHanderContainerAwareTrait;
use CoreBundle\Tests\KernelAwareTest;

/**
 * Class UserFixResultTest
 * @package CoreBundle\Tests\Handler\User
 */
class GameFixResultTest extends KernelAwareTest
{
    use GameHanderContainerAwareTrait;

    public function testFixResultGames()
    {
        $games = $this->getAllCurrentGames();
        $this->assertNotEmpty($games);
        $this->getGameHandler()->fixResultGames();

        foreach ($games as $game) {
            $this->assertEquals(GameStatus::END, $game->getStatus(), $game->getId());
            switch (true) {
                case $game->getUserToMove() == $game->getUserBlack():
                    $this->assertEquals(1, $game->getResultWhite());
                    break;
                case $game->getUserToMove() == $game->getUserWhite():
                    $this->assertEquals(1, $game->getResultBlack());
                    break;
                default:
                    throw new \Exception("Game without userToMove: " . $game->getId());
            }
        }
    }

    /**
     * @return Game[]
     */
    private function getAllCurrentGames()
    {
        return $this->getGameHandler()->getRepository()->findBy(["status" => GameStatus::PLAY]);
    }
}