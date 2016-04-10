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
use CoreBundle\Entity\User;
use CoreBundle\Model\Game\GameParams;
use CoreBundle\Model\Tournament\Params\TournamentParamsFactory;
use CoreBundle\Model\Tournament\TournamentParams;

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
        $tournament->setName($data['name']);
        
        if (isset($data['players'])) {
            foreach ($data['players'] as $referencePlayer) {
                /** @var User $player */
                $player = $this->getReference($referencePlayer);
                $tournament->addPlayer($player);
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
                $tournamentParams->setTimeBegin(new \DateTime($data["tournament_params"]["time_begin"]));
            }

            $tournament->setTournamentParams($tournamentParams);
        }

        if (isset($data["games"])) {
            foreach ($data["games"] as $dataGame) {
                /** @var Game $game */
                $game = $this->getReference($dataGame["reference"]);
                $this->container->get("core.handler.tournament")
                     ->addGameToTournament($tournament, $game, $dataGame["round"]);
            }
        }

        return $tournament;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

}