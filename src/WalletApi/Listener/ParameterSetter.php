<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

/**
 * ParameterSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class ParameterSetter implements EventSubscriberInterface
{
    public function __construct(protected array $parameters)
    {
    }

    public function onBeforeRequest(RequestEvent $event)
    {
        $options = $event->getOptions();
        $parameters = $options['parameters'] ?? [];
        $options['parameters'] = $this->parameters + $parameters;
        $event->setOptions($options);
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::BEFORE_REQUEST => ['onBeforeRequest', 100]];
    }
}
