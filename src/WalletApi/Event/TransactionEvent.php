<?php

namespace Paysera\WalletApi\Event;

/**
 * RequestEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class TransactionEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Entity\Transaction
     */
    protected $transaction;

    public function __construct(\Paysera\WalletApi\Entity\Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Gets transaction
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
