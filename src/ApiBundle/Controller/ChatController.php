<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:05
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Chat\ChatGetMessagesRequest;
use CoreBundle\Model\Request\Chat\ChatPostMessageRequest;
use CoreBundle\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class ChatController
 * @package ApiBundle\Controller
 * @RouteResource("Chat", pluralize=false)
 */
class ChatController extends BaseController
{
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
     * @return Response
     */
    public function postMessageAction(Request $request)
    {
        return $this->process($request, new ChatPostMessageRequest());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getMessagesAction(Request $request)
    {
        return $this->process($request, new ChatGetMessagesRequest());
    }
    
    /**
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.chat");
    }

}