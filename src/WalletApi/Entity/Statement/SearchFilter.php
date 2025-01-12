<?php

namespace Paysera\WalletApi\Entity\Statement;

use Paysera\WalletApi\Entity\Search\Filter;

/**
 * SearchFilter
 */
class SearchFilter extends Filter
{
    /**
     * @var \DateTime
     */
    protected $fromDate;

    /**
     * @var \DateTime
     */
    protected $toDate;

    /**
     * @var string[]
     */
    protected $currencies = [];


    public static function create()
    {
        return new self();
    }

    /**
     * Removes filtering by currency
     *
     * @return $this
     */
    public function anyCurrency()
    {
        $this->currencies = [];

        return $this;
    }

    /**
     * Adds currency to filter
     *
     *
     * @return $this
     */
    public function addCurrency($currency)
    {
        $this->currencies[] = $currency;

        return $this;
    }

    /**
     * Gets currencies for filter
     *
     * @return string[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Sets array of currencies to filter
     *
     *
     * @return $this
     */
    public function setCurrencies(array $currencies)
    {
        $this->currencies = $currencies;

        return $this;
    }

    /**
     * Sets fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return $this
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Gets fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Sets toDate
     *
     * @param \DateTime $toDate
     *
     * @return $this
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Gets toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Sets filtering period. Sets from date, subtracts period from toDate or current time if it is not defined
     *
     * @param string $intervalSpec DateInterval interval specification
     *
     * @return $this
     */
    public function setPeriod($intervalSpec)
    {
        $toDate = $this->toDate ?? new \DateTime();
        $this->setFromDate($toDate->sub(new \DateInterval($intervalSpec)));

        return $this;
    }
}
