<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

class AppendHeadersListener implements EventSubscriberInterface
{
    /**
     * @param array $headers
     */
    public function __construct(protected $headers)
    {
    }

    public function onBeforeRequest(RequestEvent $event)
    {
        foreach ($this->headers as $headerName => $headerValue) {
            $event->getRequest()->getHeaderBag()->setHeader($headerName, $headerValue);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::BEFORE_REQUEST => ['onBeforeRequest', 100]];
    }
}
