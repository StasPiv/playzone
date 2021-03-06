<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:49
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Tournament\TournamentDeleteUnrecordRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Processor\ProcessorInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TournamentController
 * @package ApiBundle\Controller
 * @RouteResource("Tournament", pluralize=false)
 */
class TournamentController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getListAction(Request $request)
    {
        return $this->process($request, new TournamentGetListRequest());
    }
    
    /**
     * @param Request $request
     * @param int $tournament_id
     * @return Response
     */
    public function getAction(Request $request, $tournament_id)
    {
        return $this->process($request, new TournamentGetRequest());
    }

    /**
     * @param Request $request
     * @param int $tournament_id
     * @return Response
     */
    public function getCurrentgameAction(Request $request, $tournament_id)
    {
        return $this->process($request, new TournamentGetCurrentgameRequest());
    }

    /**
     * @param Request $request
     * @param $tournament_id
     * @return Response
     */
    public function postRecordAction(Request $request, $tournament_id)
    {
        return $this->process($request, new TournamentPostRecordRequest(), ResponseStatusCode::CREATED);
    }

    /**
     * @param Request $request
     * @param $tournament_id
     * @return Response
     */
    public function deleteUnrecordAction(Request $request, $tournament_id)
    {
        return $this->process($request, new TournamentDeleteUnrecordRequest());
    }
    
    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->get("core.handler.tournament");
    }

}