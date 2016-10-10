<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 13:19
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\Game\GameEvent;
use CoreBundle\Model\Event\Game\GameEvents;
use CoreBundle\Model\Game\GameStatus;
use Psr\Log\LoggerInterface;
use StasPiv\EloCalculator\EloCalculator;
use StasPiv\EloCalculator\Model\EloGame;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RatingService
 * @package CoreBundle\Service
 */
class RatingService implements EventSubscriberInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const WIN = 'win';
    /**
     * @var EloCalculator
     */
    private $eloCalculator;

    /**
     * RatingService constructor.
     */
    public function __construct()
    {
        $this->eloCalculator = new EloCalculator();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            GameEvents::CHANGE_STATUS_BEFORE => [
                ['calculateGameRating', 10],
            ],
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function calculateGameRating(GameEvent $event)
    {
        if ($event->getGame()->getStatus() != GameStatus::END || !$event->getGame()->isRate()) {
            return;
        }

        if (
            $event->getGame()->getUserWhite()->isEngine() && !$event->getGame()->getUserBlack()->isEngine() ||
            !$event->getGame()->getUserWhite()->isEngine() && $event->getGame()->getUserBlack()->isEngine()
        ) {
//            return; // uncomment this line to deny rate play against robots
        }

        $game = $event->getGame();

        $eloGame = $this->getEloGame($game);

        $this->eloCalculator->calculate($eloGame);

        $this->container->get('logger')->info(
            __METHOD__ .
            '. Game #' . $game->getId().
            '. User white: '.$game->getUserWhite().
            '. User white elo: '.$eloGame->getWhiteElo().
            '. User black:'.$game->getUserBlack().
            '. User black elo:'.$eloGame->getBlackElo()
        );

        $game->getUserWhite()->setRating($eloGame->getWhiteElo());
        $game->getUserBlack()->setRating($eloGame->getBlackElo());
    }

    /**
     * @param Game $game
     * @return EloGame
     */
    private function getEloGame(Game $game)
    {
        $eloGame = new EloGame();

        $whiteUser = $game->getUserWhite();
        $blackUser = $game->getUserBlack();

        $eloGame->setWhiteElo($whiteUser->getRating())
            ->setBlackElo($blackUser->getRating())
            ->setWhiteGames($whiteUser->getRateGamesCount())
            ->setBlackGames($blackUser->getRateGamesCount())
            ->setWhiteResult($game->getResultWhite())
            ->setBlackResult($game->getResultBlack());

        return $eloGame;
    }
}