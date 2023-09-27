<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Event\MacAccessTokenEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

/**
 * BaseRefreshedTokenListener
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
abstract class BaseRefreshedTokenListener implements EventSubscriberInterface
{
    /**
     */
    public function onTokenRefresh(MacAccessTokenEvent $event)
    {
        // implement in subclasses
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::AFTER_OAUTH_TOKEN_REFRESH => 'onTokenRefresh'];
    }
}
