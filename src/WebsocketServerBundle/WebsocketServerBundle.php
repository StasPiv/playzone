<?php

namespace WebsocketServerBundle;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class WebsocketServerBundle
 * @package WebsocketServerBundle
 */
class WebsocketServerBundle extends Bundle
{
    /**
     * @var array
     */
    private $subscriberServices = [
        "ws.service.event.tournament.start_round"
    ];
    
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        parent::boot();
        
        foreach ($this->subscriberServices as $serviceName) {
            /** @var EventSubscriberInterface $subscriber */
            $subscriber = $this->container->get($serviceName);
            
            $this->container->get("event_dispatcher")->addSubscriber($subscriber);
        }
    }
}
