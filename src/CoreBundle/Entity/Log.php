<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 13:14
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\LogRepository")
 */
class Log
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
     * @ORM\Column(type="text")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $message;

    /**
     * @return int
     */
    public function getId() : int 
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Log
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string 
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Log
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
    
    
}