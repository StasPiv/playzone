<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 26.05.16
 * Time: 20:31
 */

namespace CoreBundle\Model\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface EventInterface
 * @package CoreBundle\Model\Event
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getName() : string;
    
    /**
     * Frequency in cron format
     * @link http://www.nncron.ru/help/EN/working/cron-format.htm
     *
     * @return string
     */
    public function getFrequency() : string;
}