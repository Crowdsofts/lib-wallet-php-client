<?php

namespace Paysera\WalletApi\Entity\Wallet;

/**
 * Entity representing Balance in the Wallet
 */
class Balance
{
    /**
     * @var array
     */
    protected $balanceAtDisposal = [];

    /**
     * @var array
     */
    protected $reserved = [];

    protected $balanceAtDisposalDecimal = [];

    protected $reservedDecimal = [];

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets balance for some currency. Overwrites any previous balance of same currency
     *
     * @param string  $currency
     * @param integer $amountAtDisposal
     * @param integer $amountReserved
     *
     * @return self
     */
    public function setCurrencyBalance($currency, $amountAtDisposal, $amountReserved)
    {
        $this->balanceAtDisposal[$currency] = $amountAtDisposal;
        $this->reserved[$currency] = $amountReserved;

        return $this;
    }

    /**
     * Sets balance for some currency. Overwrites any previous balance of same currency
     *
     * @param string      $currency
     * @param string|null $amountAtDisposalDecimal
     * @param string|null $amountReservedDecimal
     *
     * @return self
     */
    public function setCurrencyBalanceDecimal($currency, $amountAtDisposalDecimal, $amountReservedDecimal)
    {
        $this->balanceAtDisposalDecimal[$currency] = $amountAtDisposalDecimal;
        $this->reservedDecimal[$currency] = $amountReservedDecimal;

        return $this;
    }

    /**
     * Gets balance at disposal for provided currency in cents
     *
     * @param string $currency
     *
     * @return integer
     */
    public function getBalanceAtDisposal($currency)
    {
        return $this->balanceAtDisposal[$currency] ?? 0;
    }

    /**
     * Gets balance at disposal for provided currency
     *
     * @param string $currency
     *
     * @return string|null
     */
    public function getBalanceAtDisposalDecimal($currency)
    {
        return $this->balanceAtDisposalDecimal[$currency] ?? null;
    }


    /**
     * Gets reserved amount for provided currency in cents
     *
     * @param string $currency
     *
     * @return integer
     */
    public function getReserved($currency)
    {
        return $this->reserved[$currency] ?? 0;
    }

    /**
     * Gets reserved amount for provided currency
     *
     * @param string $currency
     *
     * @return string|null
     */
    public function getReservedDecimal($currency)
    {
        return $this->reservedDecimal[$currency] ?? null;
    }


    /**
     * Gets all currently available currencies for this balance
     *
     * @return string[]
     */
    public function getCurrencies()
    {
        return array_keys($this->balanceAtDisposal + $this->reserved);
    }
}
