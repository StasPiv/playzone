<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:43
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentParams;
use CoreBundle\Model\Tournament\TournamentStatus;

/**
 * Class TournamentFixtures
 * @package CoreBundle\DataFixtures\ORM
 */
class TournamentFixtures extends AbstractPlayzoneFixtures
{
    /**
     * @param array $data
     * @return mixed
     */
    protected function createEntity($data)
    {
        $tournament = new Tournament();
        $tournament->setName($data['name'])
                   ->setCurrentRound((int)@$data["current_round"]);

        if (isset($data["status"])) {
            $tournament->setStatus(new TournamentStatus($data["status"]));
        }
        
        $tournamentPlayersMap = [];
        if (isset($data['players'])) {
            foreach ($data['players'] as $referencePlayer) {
                /** @var User $player */
                $player = $this->getReference($referencePlayer);
                
                $tournamentPlayer = new TournamentPlayer();
                $countBlack = mt_rand(4, 5);
                $whiteInRow = mt_rand(0, 2);
                $tournamentPlayer->setTournament($tournament)
                                 ->setPlayer($player)
                                 ->setWhiteInRow($whiteInRow)
                                 ->setBlackInRow($whiteInRow ? 0 : mt_rand(0,2))
                                 ->setCountBlack($countBlack)
                                 ->setCountWhite(9 - $countBlack)
                                 ->setPoints(mt_rand(0, 9))
                                 ->setMissedRound(!!mt_rand(0,1));
                
                $opponents = $this->getShuffleOpponents($data["players"], $referencePlayer, 
                    min(count($data["players"]), 9)
                );
                
                foreach ($opponents as $opponent) {
                    $tournamentPlayer->addOpponent($opponent);
                }
                
                $tournament->getPlayers()->add($tournamentPlayer);
                $tournamentPlayersMap[$player->getId()] = $tournamentPlayer;
            }
        }

        if (isset($data["game_params"])) {
            /** @var GameParams $gameParams */
            $gameParams = $this->container->get("jms_serializer")->deserialize(
                json_encode($data["game_params"]),
                'CoreBundle\Model\Game\GameParams',
                'json'
            );

            $tournament->setGameParams($gameParams);
        }

        if (isset($data["tournament_params"])) {
            $tournamentParams = TournamentParamsFactory::create($data["tournament_params"]["type"]);
            
            if (isset($data["tournament_params"]["time_begin"])) {
                $tournamentParams->setTimeBegin(
                    $this->container->get("core.service.date")
                         ->getDateTime($data["tournament_params"]["time_begin"])
                );
            }

            $tournament->setTournamentParams($tournamentParams);
        }

        if (isset($data["games"])) {
            foreach ($data["games"] as $dataGame) {
                /** @var Game $game */
                $game = $this->getReference($dataGame["reference"]);
                $this->container->get("core.handler.tournament")
                     ->addGameToTournament(
                         $tournament, 
                         $game, 
                         $dataGame["round"], 
                         $tournamentPlayersMap[$game->getUserWhite()->getId()],
                         $tournamentPlayersMap[$game->getUserBlack()->getId()]
                     );
            }
        }

        return $tournament;
    }

    /**
     * @param array $references
     * @param string $excludeReference
     * @param int $numberOfOpponents
     * @return array|User[]
     */
    private function getShuffleOpponents(array $references, string $excludeReference, int $numberOfOpponents) : array 
    {
        $users = [];

        foreach ($references as $reference) {
            if ($reference == $excludeReference) {
                continue;
            }

            $user = $this->getReference($reference);
            
            if ($user instanceof User) {
                $users[] = $user;
            }
        }
        
        shuffle($users);
        
        return array_slice($users, 0, $numberOfOpponents);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 50;
    }

}