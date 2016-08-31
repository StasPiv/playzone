<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 31.08.16
 * Time: 20:28
 */

namespace CoreBundle\Model\Request;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RequestTrait
 * @package CoreBundle\Model\Request
 */
trait RequestTrait
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $ip = '';

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return self|static
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;

        return $this;
    }
}