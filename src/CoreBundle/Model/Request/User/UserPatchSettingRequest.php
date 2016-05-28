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
class UserPatchSettingRequest extends UserRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;
    
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
     * @JMS\Type("string")
     */
    private $value;

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
        return (string)$this->value;
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