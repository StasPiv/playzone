<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.01.16
 * Time: 23:55
 */

namespace ApiBundle\Controller;

use ApiBundle\Model\Request\Game\GameGetListRequest;
use ApiBundle\Model\Request\Game\GamePostCallRequest;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Processor\GameProcessorInterface;

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

    public function postCallAction(Request $request)
    {
        return $this->process($request, new GamePostCallRequest());
    }

    /**
     * @return GameProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game");
    }
}