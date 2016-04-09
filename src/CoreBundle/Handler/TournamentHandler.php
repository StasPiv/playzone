<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:54
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\Tournament;
use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
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
     * @param TournamentGetListRequest $listRequest
     * @return Tournament[]
     */
    public function processGetList(TournamentGetListRequest $listRequest) : array
    {
        return $this->repository->findAll();
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
        $tournament->removePlayer($user);

        $this->manager->persist($tournament);

        return $tournament;
    }

}