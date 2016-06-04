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
class UserPatchLagRequest extends UserRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Type("float")
     *
     * @Assert\NotBlank(
     *     message = "Lag is required for this request"
     * )
     */
    private $lag;

    /**
     * @return float
     */
    public function getLag()
    {
        return $this->lag;
    }

    /**
     * @param float $lag
     * @return UserPatchLagRequest
     */
    public function setLag($lag)
    {
        $this->lag = $lag;

        return $this;
    }
}