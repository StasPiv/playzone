<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 17:04
 */

namespace CoreBundle\Model\Request;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SecurityRequestAwareTrait
 * @package CoreBundle\Model\Request
 */
trait SecurityRequestAwareTrait
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Login is required for this request"
     * )
     */
    protected $login;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Token is required for this request"
     * )
     */
    protected $token;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
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
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}