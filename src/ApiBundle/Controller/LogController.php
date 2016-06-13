<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 12:58
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Log\LogPostRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Processor\ProcessorInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogController
 * @package ApiBundle\Controller
 * @RouteResource("Log", pluralize=false)
 */
class LogController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        return $this->process($request, new LogPostRequest(), ResponseStatusCode::CREATED);
    }
    
    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->get("core.handler.log");
    }

}