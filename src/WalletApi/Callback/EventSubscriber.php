<?php

namespace Paysera\WalletApi\Callback;

use Paysera\WalletApi\Event\TransactionEvent;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;

/**
 * Can be used as base class for your own callback event subscriber
 */
abstract class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return ['transaction.failed' => 'onTransactionFailed', 'transaction.rejected' => 'onTransactionRejected', 'transaction.reserved' => 'onTransactionReserved', 'transaction.waiting_funds' => 'onTransactionWaitingFunds', 'transaction.confirmed' => 'onTransactionConfirmed', 'transaction.waiting_registration' => 'onTransactionWaitingRegistration', 'transaction.waiting_password' => 'onTransactionWaitingPassword'];
    }

    /**
     * Gets called when transaction has failed
     */
    public function onTransactionFailed(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction has been rejected
     */
    public function onTransactionRejected(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved
     */
    public function onTransactionReserved(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction is reserved with missing funds
     */
    public function onTransactionWaitingFunds(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved and transaction was confirmed automatically
     */
    public function onTransactionConfirmed(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved, but at least one of beneficiaries is not yet registered
     */
    public function onTransactionWaitingRegistration(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved, but at least one payment password is pending
     */
    public function onTransactionWaitingPassword(TransactionEvent $event): void
    {
        // does nothing - method for overriding
    }
}
