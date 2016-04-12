<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:54
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\TournamentGame;
use CoreBundle\Entity\TournamentPlayer;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Exception\Handler\Tournament\TournamentPlayerNotFoundException;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Processor\TournamentProcessorInterface;
use CoreBundle\Repository\TournamentRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TournamentHandler
 * @package CoreBundle\Handler
 */
class TournamentHandler implements TournamentProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var TournamentRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Tournament');
    }

    /**
     * @return TournamentRepository
     */
    public function getRepository() : TournamentRepository
    {
        return $this->repository;
    }
    
    /**
     * @param TournamentGetListRequest $listRequest
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $listRequest) : array
    {
        $tournaments = $this->repository->findAll();

        if ($listRequest->getToken()) {
            $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
                $listRequest,
                $this->getRequestError()
            );

            foreach ($tournaments as $tournament) {
                $this->setMineToTournament($tournament, $user);
            }
        }

        return $tournaments;
    }

    /**
     * @param TournamentGetRequest $getRequest
     * @return Tournament
     */
    public function processGet(TournamentGetRequest $getRequest) : Tournament
    {
        try {
            $tournament = $this->repository->find($getRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        return $tournament;
    }

    /**
     * @param TournamentPostRecordRequest $recordRequest
     * @return Tournament
     */
    public function processPostRecord(TournamentPostRecordRequest $recordRequest) : Tournament
    {
        $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
            $recordRequest,
            $this->getRequestError()
        );

        try {
            $tournament = $this->repository->find($recordRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                                    ->throwException(ResponseStatusCode::NOT_FOUND);
        }
        
        /** @var Tournament $tournament */
        $tournament->addPlayer($user);

        $this->manager->persist($tournament);
        $this->manager->flush();

        $this->setMineToTournament($tournament, $user);

        return $tournament;
    }

    /**
     * @param TournamentDeleteUnrecordRequest $unrecordRequest
     * @return Tournament
     */
    public function processDeleteUnrecord(TournamentDeleteUnrecordRequest $unrecordRequest) : Tournament
    {
        $user = $this->container->get("core.service.security")->getUserIfCredentialsIsOk(
            $unrecordRequest,
            $this->getRequestError()
        );

        try {
            $tournament = $this->repository->find($unrecordRequest->getTournamentId());
        } catch (TournamentNotFoundException $e) {
            $this->getRequestError()->addError("tournament_id", "Tournament is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var Tournament $tournament */
        try {
            $tournamentPlayer = $this->searchTournamentPlayer($tournament, $user);
        } catch (TournamentPlayerNotFoundException $e) {
            return $tournament;
        }

        $this->manager->remove($tournamentPlayer);

        $this->manager->persist($tournament);
        $this->manager->flush();

        $this->setMineToTournament($tournament, $user);

        return $tournament;
    }

    /**
     * @param Tournament $tournament
     * @param Game $game
     * @param int $round
     */
    public function addGameToTournament(Tournament $tournament, Game $game, int $round = 0)
    {
        $tournamentGame = (new TournamentGame())->setTournament($tournament)
                                                ->setGame($game)
                                                ->setRound($round);
        
        $this->manager->persist($tournamentGame);
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     */
    private function setMineToTournament(Tournament $tournament, User $user)
    {
        try {
            $this->searchTournamentPlayer($tournament, $user);
            $tournament->setMine(true);
        } catch (TournamentPlayerNotFoundException $e) {
            $tournament->setMine(false);
        }
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return TournamentPlayer
     */
    private function searchTournamentPlayer(Tournament $tournament, User $user) : TournamentPlayer
    {
        return $this->manager->getRepository("CoreBundle:TournamentPlayer")
                    ->findByTournamentAndUser($tournament, $user);
    }

}