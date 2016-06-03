<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.04.16
 * Time: 11:21
 */

namespace CoreBundle\Model\Request\User;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserPatchSettingRequest
 * @package CoreBundle\Model\Request\User
 */
class UserPatchPingRequest extends UserRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Type("float")
     *
     * @Assert\NotBlank(
     *     message = "Ping is required for this request"
     * )
     */
    private $ping;

    /**
     * @return float
     */
    public function getPing()
    {
        return $this->ping;
    }

    /**
     * @param float $ping
     * @return UserPatchPingRequest
     */
    public function setPing($ping)
    {
        $this->ping = $ping;

        return $this;
    }
}