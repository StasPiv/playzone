<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 25.05.16
 * Time: 15:57
 */

namespace CoreBundle\Model\Event;

/**
 * Interface EventInterface
 * @package CoreBundle\Model\Event
 */
interface EventCommandInterface
{
    /**
     * @return void
     */
    public function run();

    /**
     * @param EventInterface $eventModel
     */
    public function setEventModel(EventInterface $eventModel);
}