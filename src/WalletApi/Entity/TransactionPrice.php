<?php

namespace Paysera\WalletApi\Entity;

class TransactionPrice
{
    /**
     * @var int
     */
    protected $paymentId;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $price;

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Sets paymentId
     *
     * @param int $paymentId
     *
     * @return self
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Gets paymentId
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Sets price
     *
     * @return self
     */
    public function setPrice(\Paysera\WalletApi\Entity\Money $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets price
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getPrice()
    {
        return $this->price;
    }
}
