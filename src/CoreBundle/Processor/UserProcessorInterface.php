<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:07
 */

namespace CoreBundle\Processor;


use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Entity\User;

interface UserProcessorInterface extends ProcessorInterface
{
    /**
     * @param UserPostRegisterRequest $registerRequest
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest) : User;

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest) : User;

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest) : array;
}