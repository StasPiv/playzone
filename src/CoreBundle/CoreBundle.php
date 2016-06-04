<?php

namespace CoreBundle;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CoreBundle
 * @package CoreBundle
 */
class CoreBundle extends Bundle
{
    /**
     * @var array
     */
    private $subscriberServices = [
        "core.handler.tournament",
        "core.handler.user"
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
