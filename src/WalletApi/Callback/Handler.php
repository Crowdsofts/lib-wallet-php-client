<?php

namespace Paysera\WalletApi\Callback;

use Paysera\WalletApi\Event\AllowanceEvent;
use Paysera\WalletApi\Event\PaymentEvent;
use Paysera\WalletApi\Event\TransactionEvent;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Exception\CallbackException;
use Paysera\WalletApi\Exception\CallbackUnsupportedException;
use Paysera\WalletApi\Mapper;

class Handler
{
    /**
     * Constructs object
     */
    public function __construct(
        protected EventDispatcher $eventDispatcher,
        protected SignChecker $callbackSignChecker,
        protected Mapper $mapper
    ) {
    }

    /**
     * @param array<string, mixed> $post
     *
     * @throws CallbackException
     * @throws CallbackUnsupportedException
     * @throws \JsonException
     */
    public function handle(array $post): void
    {
        if (!isset($post['event'], $post['sign'])) {
            throw new CallbackException('At least one of required parameters is missing');
        }
        $event = $post['event'];
        $sign = $post['sign'];

        if (!$this->callbackSignChecker->checkSign($event, $sign)) {
            throw new CallbackException('Sign validation failed');
        }

        $eventData = \json_decode($event, true, 512, JSON_THROW_ON_ERROR);

        try {
            if ($eventData['object'] === 'transaction') {
                $subject = new TransactionEvent(
                    $this->mapper->decodeTransaction($eventData['data']),
                );
            } elseif ($eventData['object'] === 'payment') {
                $subject = new PaymentEvent(
                    $this->mapper->decodePayment($eventData['data']),
                );
            } elseif ($eventData['object'] === 'allowance') {
                $subject = new AllowanceEvent(
                    $this->mapper->decodeAllowance($eventData['data']),
                );
            } else {
                throw new CallbackUnsupportedException('Unknown event object');
            }
        } catch (CallbackException $exception) {
            throw $exception;                                     // just pass callback exceptions
        } catch (\Exception $exception) {
            throw new CallbackException(    // wrap other exceptions to callback exception
                'Exception caught while trying to decode event subject',
                0,
                $exception,
            );
        }

        $eventKey = $eventData['object'] . '.' . $eventData['type'];
        $this->eventDispatcher->dispatch($eventKey, $subject);
    }
}
