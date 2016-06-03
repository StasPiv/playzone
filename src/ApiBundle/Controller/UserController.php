<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:37
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\User\UserGetProfileRequest;
use CoreBundle\Model\Request\User\UserPatchPingRequest;
use CoreBundle\Model\Request\User\UserPatchSettingRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
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
    /**
     * @param Request $request
     * @return Response
     */
    public function postRegisterAction(Request $request)
    {
        return $this->process($request, new UserPostRegisterRequest());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function postAuthAction(Request $request)
    {
        return $this->process($request, new UserPostAuthRequest());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getListAction(Request $request)
    {
        return $this->process($request, new UserGetListRequest());
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function getProfileAction(Request $request, $id)
    {
        return $this->process($request, new UserGetProfileRequest());
    }

    /**
     * @param Request $request
     * @param int|string $setting_id
     * @return Response
     */
    public function patchSettingAction(Request $request, $setting_id)
    {
        return $this->process($request, new UserPatchSettingRequest());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function patchPingAction(Request $request)
    {
        return $this->process($request, new UserPatchPingRequest());
    }

    /**
     * @return UserProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.user");
    }
}