<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 21:02
 */

namespace CoreBundle\Model\Event;

/**
 * Class EventFrequencyAwareTrait
 * @package CoreBundle\Model\Event
 */
trait EventFrequencyAwareTrait
{
    /**
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $frequency;

    /**
     * @param string $frequency
     * @return $this
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Frequency in cron format
     * @link http://www.nncron.ru/help/EN/working/cron-format.htm
     *
     * @return string
     */
    public function getFrequency() : string
    {
        return $this->frequency;
    }
}