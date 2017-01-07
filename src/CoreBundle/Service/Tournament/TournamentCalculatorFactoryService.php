<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.17
 * Time: 23:42
 */

namespace CoreBundle\Service\Tournament;

use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentIncorrectTypeException;
use CoreBundle\Model\Tournament\TournamentCalculatorInterface;
use CoreBundle\Model\Tournament\TournamentType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TournamentCalculatorFactoryService
 * @package CoreBundle\Service\Tournament
 */
class TournamentCalculatorFactoryService
{
    use ContainerAwareTrait;

    /**
     * @param Tournament $tournament
     * @return TournamentCalculatorInterface
     * @throws TournamentIncorrectTypeException
     */
    public function create(Tournament $tournament) : TournamentCalculatorInterface
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