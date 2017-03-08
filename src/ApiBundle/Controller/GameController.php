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
use CoreBundle\Model\Request\Game\GamePostAddmessageRequest;
use CoreBundle\Model\Request\Game\GamePostNewrobotRequest;
use CoreBundle\Model\Request\Game\GamePostPublishRequest;
use CoreBundle\Model\Request\Game\GamePutAbortRequest;
use CoreBundle\Model\Request\Game\GamePutAcceptdrawRequest;
use CoreBundle\Model\Request\Game\GamePutCountEventsRequest;
use CoreBundle\Model\Request\Game\GamePutFixRequest;
use CoreBundle\Model\Request\Game\GamePutOfferdrawRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Game\GamePutResignRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
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
     *  description="Save pgn",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"},
     *      {"name"="count_switching_white", "dataType"="integer"},
     *      {"name"="count_switching_black", "dataType"="integer"},
     *      {"name"="count_mouse_leave_white", "dataType"="integer"},
     *      {"name"="count_mouse_leave_black", "dataType"="integer"}
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function putCountEventsAction(Request $request, $id)
    {
        return $this->process($request, new GamePutCountEventsRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Publish game"
     * )
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function postPublishAction(Request $request, $id)
    {
        return $this->process($request, new GamePostPublishRequest(), ResponseStatusCode::CREATED);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Save pgn",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"}
     *  }
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function postNewrobotAction(Request $request)
    {
        return $this->process($request, new GamePostNewrobotRequest());
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
    public function putAcceptdrawAction(Request $request, $id)
    {
        return $this->process($request, new GamePutAcceptdrawRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
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
    public function postAddmessageAction(Request $request, $id)
    {
        return $this->process($request, new GamePostAddmessageRequest());
    }
    
    /**
     * @return GameProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game");
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Fix result",
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
    public function putFixAction(Request $request, $id)
    {
        return $this->process($request, new GamePutFixRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Abort game",
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
    public function putAbortAction(Request $request, $id)
    {
        return $this->process($request, new GamePutAbortRequest());
    }
}