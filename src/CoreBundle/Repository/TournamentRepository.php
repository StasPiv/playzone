<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:37
 */

namespace CoreBundle\Repository;

use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * Class TournamentRepository
 * @package CoreBundle\Repository
 */
class TournamentRepository extends EntityRepository
{
    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     * @throws TournamentNotFoundException
     */
    public function find($id, $lockMode = null, $lockVersion = null) : Tournament
    {
        $tournament = parent::find($id, $lockMode, $lockVersion);
        
        if (!$tournament instanceof Tournament) {
            throw new TournamentNotFoundException;
        }

        return $tournament;
    }

    /**
     * @param string $name
     * @return Tournament
     * @throws TournamentNotFoundException
     */
    public function findOneByName(string $name) : Tournament
    {
        $tournament = parent::findOneByName($name);

        if (!$tournament instanceof Tournament) {
            throw new TournamentNotFoundException;
        }

        return $tournament;
    }

}