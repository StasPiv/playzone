<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:13
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Problem\ProblemGetRandomRequest;
use CoreBundle\Model\Request\Problem\ProblemGetRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class ProblemController
 * @package ApiBundle\Controller
 *
 * @RouteResource("Problem")
 */
class ProblemController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getRandomAction(Request $request) : Response
    {
        return $this->process($request, new ProblemGetRandomRequest());
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function getAction(Request $request, int $id) : Response
    {
        return $this->process($request, new ProblemGetRequest());
    }

    /**
     * @inheritDoc
     */
    protected function getProcessor()
    {
        return $this->get("core.handler.problem");
    }

}