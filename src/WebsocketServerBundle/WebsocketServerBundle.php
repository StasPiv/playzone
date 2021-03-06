<?php

namespace WebsocketServerBundle;

use CoreBundle\CoreBundle;

/**
 * Class WebsocketServerBundle
 * @package WebsocketServerBundle
 */
class WebsocketServerBundle extends CoreBundle
{
    /**
     * @var array
     */
    protected $subscriberServices = [
        "ws.service.event.tournament.start_round",
        "ws.service.event.tournament.new_tournament_listener"
    ];
}
