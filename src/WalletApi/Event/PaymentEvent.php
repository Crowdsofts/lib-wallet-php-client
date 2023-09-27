<?php

namespace Paysera\WalletApi\Event;

/**
 * PaymentEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class PaymentEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Entity\Payment
     */
    protected $payment;

    public function __construct(\Paysera\WalletApi\Entity\Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Gets payment
     *
     * @return \Paysera\WalletApi\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
