<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.03.16
 * Time: 16:30
 */

namespace CoreBundle\Model\Request\Call\CallSend;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class Time
{
    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $base;
    
    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $increment;

    /**
     * @return int
     */
    public function getBase() : int
    {
        return $this->base;
    }

    /**
     * @param int $base
     * @return Time
     */
    public function setBase($base) : Time
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @return int
     */
    public function getIncrement() : int 
    {
        return (int)$this->increment;
    }

    /**
     * @param int $increment
     * @return Time
     */
    public function setIncrement($increment) : self 
    {
        $this->increment = $increment;

        return $this;
    }
}