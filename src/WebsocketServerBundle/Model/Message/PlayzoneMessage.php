<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.01.16
 * Time: 17:28
 */

namespace WebsocketServerBundle\Model\Message;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PlayzoneMessage
 * @package WebsocketServerBundle\Model\Message
 */
class PlayzoneMessage
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Type is required"
     * )
     */
    protected $scope;
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Method is required"
     * )
     */
    protected $method;
    /**
     * @var array
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    protected $data;
    /**
     * @var array
     *
     * @JMS\Expose()
     * @JMS\Type("array")
     */
    protected $logins;

    /**
     * @var int
     * 
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    protected $ms;

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getLogins()
    {
        return $this->logins;
    }

    /**
     * @param array $logins
     * @return $this
     */
    public function setLogins($logins)
    {
        $this->logins = $logins;

        return $this;
    }

    /**
     * @return int
     */
    public function getMs()
    {
        return $this->ms;
    }

    /**
     * @param int $ms
     * @return PlayzoneMessage
     */
    public function setMs($ms)
    {
        $this->ms = $ms;

        return $this;
    }
}