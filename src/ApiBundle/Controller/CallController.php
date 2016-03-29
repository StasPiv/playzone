<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:03
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Call\CallDeleteDeclineRequest;
use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallGetRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Request\Call\CallSend\CallPostSendRequestError;
use CoreBundle\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class CallController
 * @package ApiBundle\Controller
 * @RouteResource("Call", pluralize=false)
 */
class CallController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        return $this->process($request, new CallGetRequest());
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Post call to another player",
     *  filters={
     *      {"name"="login", "dataType"="string", "description"="Your name"},
     *      {"name"="token", "dataType"="string", "description"="Your token"},
     *      {"name"="player", "dataType"="string"},
     *      {"name"="color", "dataType"="string"},
     *      {"name"="games_count", "dataType"="integer"}
     *  }
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postSendAction(Request $request)
    {
        return $this->process($request, new CallPostSendRequest());
    }

    /**
     * @param Request $request
     * @param $call_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRemoveAction(Request $request, $call_id)
    {
        return $this->process($request, new CallDeleteRemoveRequest());
    }

    /**
     * @param Request $request
     * @param $call_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAcceptAction(Request $request, $call_id)
    {
        return $this->process($request, new CallDeleteAcceptRequest());
    }

    /**
     * @param Request $request
     * @param $call_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteDeclineAction(Request $request, $call_id)
    {
        return $this->process($request, new CallDeleteDeclineRequest());
    }

    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game.call");
    }
}