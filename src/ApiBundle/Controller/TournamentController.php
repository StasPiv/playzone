<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:49
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Tournament\TournamentGetListRequest;
use CoreBundle\Processor\ProcessorInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TournamentController
 * @package ApiBundle\Controller
 * @RouteResource("Tournament", pluralize=false)
 */
class TournamentController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListAction(Request $request)
    {
        return $this->process($request, new TournamentGetListRequest());
    }
    
    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->get("core.handler.tournament");
    }

}