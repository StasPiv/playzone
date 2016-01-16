<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:37
 */

namespace ApiBundle\Controller;

use ApiBundle\Model\Request\User\UserPostAuthRequest;
use ApiBundle\Model\Request\User\UserGetListRequest;
use ApiBundle\Model\Request\User\UserPostRegisterRequest;
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
        return $this->process($request, new UserPostRegisterRequest());
    }

    public function postAuthAction(Request $request)
    {
        return $this->process($request, new UserPostAuthRequest());
    }

    public function getListAction(Request $request)
    {
        return $this->process($request, new UserGetListRequest());
    }

    /**
     * @return UserProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.user");
    }
}