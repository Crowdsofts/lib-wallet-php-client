<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Event\ResponseExceptionEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Events;

class InvalidResponseListener implements EventSubscriberInterface
{
    public function onResponseException(ResponseExceptionEvent $event)
    {
        $options = $event->getOptions();

        if (
            $event->getException()->getStatusCode() === 502
            && !$event->isRepeatRequest()
            && (!isset($options['isRepeated']) || $options['isRepeated'] === false)
        ) {
            $event->setRepeatRequest(true);
            $event->setOptions(array_merge(
                $event->getOptions(),
                ['isRepeated' => true],
            ));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [Events::ON_RESPONSE_EXCEPTION => 'onResponseException'];
    }
}
