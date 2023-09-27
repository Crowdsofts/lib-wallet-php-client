<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Auth\SignerInterface;
use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

/**
 * RequestSigner
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class RequestSigner implements EventSubscriberInterface
{
    public function __construct(protected SignerInterface $signer)
    {
    }

    public function onBeforeRequest(RequestEvent $event)
    {
        $options = $event->getOptions();
        $parameters = $options['parameters'] ?? [];
        $this->signer->signRequest($event->getRequest(), $parameters);
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::BEFORE_REQUEST => ['onBeforeRequest', -100]];
    }
}
