<?php

namespace ImmortalchessNetBundle;

use CoreBundle\CoreBundle;

/**
 * Class ImmortalchessNetBundle
 * @package ImmortalchessNetBundle
 */
class ImmortalchessNetBundle extends CoreBundle
{
    /**
     * @var array
     */
    protected $subscriberServices = [
        "immortalchessnet.service.immortalchessnet"
    ];
}
