<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.04.16
 * Time: 11:21
 */

namespace CoreBundle\Model\Request\User;

use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserPatchSettingRequest
 * @package CoreBundle\Model\Request\User
 */
class UserPatchSettingRequest extends UserRequest implements SecurityRequestInterface
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Login is required for this request"
     * )
     */
    private $login;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Token is required for this request"
     * )
     */
    private $token;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     *
     * @Assert\NotBlank(
     *     message = "Setting id is required for this request"
     * )
     */
    private $settingId;

    /**
     * @var string
     * 
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $value;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return void
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getSettingId() : int
    {
        return $this->settingId;
    }

    /**
     * @param int $settingId
     * @return UserPatchSettingRequest
     */
    public function setSettingId(int $settingId)
    {
        $this->settingId = $settingId;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue() : string 
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return UserPatchSettingRequest
     */
    public function setValue(string $value) : UserPatchSettingRequest
    {
        $this->value = $value;

        return $this;
    }
}