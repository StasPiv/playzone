<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:55
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Game\GameGetListRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Processor\GameProcessorInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class GameController
 * @package ApiBundle\Controller
 * @RouteResource("Game", pluralize=false)
 */
class GameController extends BaseController
{
    public function getListAction(Request $request)
    {
        return $this->process($request, new GameGetListRequest());
    }

    public function getAction(Request $request, $id)
    {
        return $this->process($request, new GameGetRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Save pgn",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"},
     *      {"name"="pgn", "dataType"="string"}
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function putPgnAction(Request $request, $id)
    {
        return $this->process($request, new GamePutPgnRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Resign game",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"}
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function putResignAction(Request $request, $id)
    {
        return $this->process($request, new GamePutResignRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Resign game",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"}
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function putOfferdrawAction(Request $request, $id)
    {
        return $this->process($request, new GamePutOfferdrawRequest());
    }

    /**
     * @return GameProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game");
    }
}