<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

/**
 * EndpointSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class EndpointSetter implements EventSubscriberInterface
{
    public function __construct(protected string $endpoint)
    {
    }

    public function onBeforeRequest(RequestEvent $event)
    {
        $uri = $event->getRequest()->getFullUri();
        if (!str_starts_with($uri, 'http://') && !str_starts_with($uri, 'https://')) {
            $event->getRequest()->setFullUri($this->endpoint . $uri);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::BEFORE_REQUEST => ['onBeforeRequest', 100]];
    }
}
