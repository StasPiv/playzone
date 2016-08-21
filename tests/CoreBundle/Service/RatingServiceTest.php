<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 13:27
 */

namespace CoreBundle\Tests\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class RatingServiceTest
 * @package CoreBundle\Tests\Service
 */
class RatingServiceTest extends KernelTestCase
{
    /**
     * @var RatingService
     */
    private $service;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get("core.service.rating");
    }

    /**
     *
     */
    public function testRatingCalculate()
    {
        $game = new Game();

        $userA = $this->container->get('core.handler.user')->getRepository()->findOneByLogin('Stas');

        $userB = $this->container->get('core.handler.user')->getRepository()->findOneByLogin('TestLogin');

        $game->setUserWhite($userA)->setUserBlack($userB)->setResultWhite(1)->setResultBlack(0)->setUserToMove($userA)->setStatus(GameStatus::END)->setRate(true);

        $this->container->get('core.handler.game')->changeGameStatus($game, GameStatus::END);

        self::assertEquals(2211, $game->getUserWhite()->getRating());
        self::assertEquals(2232, $game->getUserBlack()->getRating());
    }
}