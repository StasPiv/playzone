<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:37
 */

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Processor\UserProcessorInterface;

/**
 * Class UserController
 * @package ApiBundle\Controller
 * @RouteResource("User", pluralize=false)
 */
class UserController extends BaseController
{
    public function postRegisterAction(Request $request)
    {
        return $this->process($request);
    }

    public function postAuthAction(Request $request)
    {
        return $this->process($request);
    }

    public function getListAction(Request $request)
    {
        return $this->process($request);
    }

    /**
     * @return UserProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.user");
    }
}