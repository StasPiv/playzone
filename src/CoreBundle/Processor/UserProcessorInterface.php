<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:07
 */

namespace CoreBundle\Processor;


use ApiBundle\Model\Request\User\UserGetListRequest;
use ApiBundle\Model\Request\User\UserPostAuthRequest;
use ApiBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Entity\User;

interface UserProcessorInterface extends ProcessorInterface
{
    /**
     * @param UserPostRegisterRequest $registerRequest
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest);

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $authRequest);

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest);
}