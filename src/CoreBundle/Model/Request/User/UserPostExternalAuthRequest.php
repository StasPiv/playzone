<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 16:31
 */

namespace CoreBundle\Model\Request\User;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserPostAuthRequest
 * @package CoreBundle\Model\Request\User
 */
class UserPostExternalAuthRequest extends UserRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Enter login"
     * )
     */
    protected $externalLogin;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $token;
    
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Enter password"
     * )
     */
    private $password;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getExternalLogin(): string
    {
        return $this->externalLogin;
    }

    /**
     * @param string $externalLogin
     * @return UserPostExternalAuthRequest
     */
    public function setExternalLogin(string $externalLogin): self
    {
        $this->externalLogin = $externalLogin;

        return $this;
    }
}