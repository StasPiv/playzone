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

    public function getAction(Request $request, $id)
    {
        return $this->process($request, new GameGetRequest());
    }

    /**
     * @return GameProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.game");
    }
}