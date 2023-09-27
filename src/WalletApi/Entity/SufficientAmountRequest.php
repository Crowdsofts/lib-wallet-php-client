<?php

namespace Paysera\WalletApi\Entity;

class SufficientAmountRequest
{
    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    private $amount;

    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Money $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
