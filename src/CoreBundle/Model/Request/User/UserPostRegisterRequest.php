<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 16:54
 */

namespace CoreBundle\Model\Request\User;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class UserPostRegisterRequest extends UserRequest
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Login must contain at least {{ limit }} symbols",
     *      maxMessage = "Login must not contain greater than {{ limit }} symbols"
     * )
     * @Assert\NotBlank(
     *     message = "Enter login"
     * )
     * @Assert\Regex("/^[\d\-_\wа-яА-Я]+$/", message="Login must contain only words, numbers, underscores and dashes")
     */
    private $login;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\Email(
     *     message = "E-mail is not correct",
     *     checkMX = true
     * )
     * @Assert\NotBlank(
     *     message = "Enter e-mail"
     * )
     *
     */
    private $email;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\Length(
     *      min = 6,
     *      max = 15,
     *      minMessage = "The password must contain at least {{ limit }} symbols",
     *      maxMessage = "The password must not contain greater than {{ limit }} symbols"
     * )
     * @Assert\NotBlank(
     *     message = "Enter password"
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Enter password repeat"
     * )
     */
    private $passwordRepeat;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

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
    public function getPasswordRepeat()
    {
        return $this->passwordRepeat;
    }

    /**
     * @param string $passwordRepeat
     */
    public function setPasswordRepeat($passwordRepeat)
    {
        $this->passwordRepeat = $passwordRepeat;
    }
}