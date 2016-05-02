<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:37
 */

namespace CoreBundle\Model\Request\User;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGetProfileRequest
 * @package CoreBundle\Model\Request\User
 */
class UserGetProfileRequest extends UserRequest
{
    /**
     * @var integer
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserGetProfileRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}