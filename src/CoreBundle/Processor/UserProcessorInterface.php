<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:07
 */

namespace CoreBundle\Processor;


use CoreBundle\Entity\UserSetting;
use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserGetProfileRequest;
use CoreBundle\Model\Request\User\UserPatchLagRequest;
use CoreBundle\Model\Request\User\UserPatchSettingRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserPostExternalAuthRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Entity\User;

interface UserProcessorInterface extends ProcessorInterface
{
    /**
     * @param UserPostRegisterRequest $request
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $request) : User;

    /**
     * @param UserPostAuthRequest $request
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $request) : User;

    /**
     * @param UserPostExternalAuthRequest $request
     * @return User
     */
    public function processPostExternalAuth(UserPostExternalAuthRequest $request) : User;

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest) : array;

    /**
     * @param UserGetProfileRequest $request
     * @return User
     */
    public function processGetProfile(UserGetProfileRequest $request) : User;

    /**
     * @param UserPatchSettingRequest $settingRequest
     * @return UserSetting
     */
    public function processPatchSetting(UserPatchSettingRequest $settingRequest) : UserSetting;

    /**
     * @param UserPatchLagRequest $request
     * @return User
     */
    public function processPatchLag(UserPatchLagRequest $request) : User;
}