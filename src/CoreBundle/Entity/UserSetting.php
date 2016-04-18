<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 14.04.16
 * Time: 10:31
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class UserSetting
 * @package CoreBundle\Entity
 * 
 * @ORM\Table(name="user_setting")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\UserSettingRepository")
 */
class UserSetting
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @JMS\Expose
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     *
     * @JMS\Expose
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\SerializedName("value")
     * @JMS\Type("string")
     */
    private $value;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string 
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserSetting
     */
    public function setName(string $name) : UserSetting
    {
        $this->name = $name;

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
     * @return UserSetting
     */
    public function setValue(string $value) : UserSetting
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return UserSetting
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }
}