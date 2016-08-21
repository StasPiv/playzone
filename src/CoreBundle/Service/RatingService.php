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
use StasPiv\EloCalculator\EloCalculator;
use StasPiv\EloCalculator\Model\EloGame;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RatingService
 * @package CoreBundle\Service
 */
class RatingService implements EventSubscriberInterface
{
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

        $game = $event->getGame();

        $this->updateTotals($game);

        $eloGame = $this->getEloGame($game);

        $this->eloCalculator->calculate($eloGame);

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
            ->setWhiteGames($whiteUser->getWin() + $whiteUser->getDraw() + $whiteUser->getLose())
            ->setBlackGames($whiteUser->getWin() + $whiteUser->getDraw() + $whiteUser->getLose())
            ->setWhiteResult($game->getResultWhite())
            ->setBlackResult($game->getResultBlack());

        return $eloGame;
    }

    /**
     * @param Game $game
     */
    private function updateTotals(Game $game)
    {
        $white = $game->getUserWhite();
        $black = $game->getUserBlack();

        switch ($game->getResultWhite()) {
            case 1:
                $white->setWin($white->getWin() + 1);
                $black->setLose($black->getLose() + 1);
                break;
            case 0.5:
                $white->setDraw($white->getDraw() + 1);
                $black->setDraw($black->getDraw() + 1);
                break;
            case 0:
                $white->setLose($white->getLose() + 1);
                $black->setWin($black->getWin() + 1);
                break;
        }
    }
}