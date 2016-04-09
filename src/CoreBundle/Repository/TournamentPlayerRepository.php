<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 21:09
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Tournament\TournamentPlayerNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * Class TournamentPlayerRepository
 * @package CoreBundle\Repository
 */
class TournamentPlayerRepository extends EntityRepository
{
    /**
     * @param Tournament $tournament
     * @param User $user
     * @return TournamentPlayer
     * @throws TournamentPlayerNotFoundException
     */
    public function findByTournamentAndUser(Tournament $tournament, User $user) : TournamentPlayer
    {
        $tournamentPlayer = $this->findOneBy(
            [
                "player" => $user,
                "tournament" => $tournament
            ]
        );
        
        if (!$tournamentPlayer instanceof TournamentPlayer) {
            throw new TournamentPlayerNotFoundException;
        }
        
        return $tournamentPlayer;
    }
}