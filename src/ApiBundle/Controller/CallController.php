<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 11:03
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Call\CallDeleteRemoveRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallPutAcceptRequest;
use CoreBundle\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class CallController
 * @package ApiBundle\Controller
 * @RouteResource("Call", pluralize=false)
 */
class CallController extends BaseController
{
    public function postSendAction(Request $request)
    {
        return $this->process($request, new CallPostSendRequest());
    }

    public function deleteRemoveAction(Request $request)
    {
        return $this->process($request, new CallDeleteRemoveRequest());
    }

    public function putAcceptAction(Request $request)
    {
        return $this->process($request, new CallPutAcceptRequest());
    }

    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game.call");
    }
}