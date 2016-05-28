<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.05.16
 * Time: 23:24
 */

namespace CoreBundle\Service\Tournament;


use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentIncorrectTypeException;
use CoreBundle\Model\Tournament\Params\TournamentSwitzParams;
use CoreBundle\Model\Tournament\TournamentDrawInterface;
use CoreBundle\Model\Tournament\TournamentType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TournamentDrawFactoryService
 * @package CoreBundle\Service\Tournament
 */
class TournamentDrawFactoryService
{
    use ContainerAwareTrait;
    
    /**
     * @param Tournament $tournament
     * @return TournamentDrawInterface
     * @throws TournamentIncorrectTypeException
     */
    public function create(Tournament $tournament) : TournamentDrawInterface
    {
        switch ($tournament->getTournamentParams()->getType()) {
            case TournamentType::ROUND_ROBIN():
                return $this->container->get("core.service.round_robin");
            case TournamentType::SWITZ():
                return $this->container->get("core.service.swiss");
            default:
                throw new TournamentIncorrectTypeException;
        }
    }
}