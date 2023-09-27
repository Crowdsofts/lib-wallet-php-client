<?php

namespace Paysera\WalletApi\Entity;

use Paysera\WalletApi\Util\Assert;

/**
 * Entity representing Limit for Allowance
 */
class Limit
{
    /**
     * @var Money
     */
    protected $maxPrice;

    /**
     * @var integer time in seconds
     */
    protected $time;

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
     * Sets maxPrice
     *
     * @return self
     */
    public function setMaxPrice(Money $maxPrice)
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * Gets maxPrice
     *
     * @return Money
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * @param integer $time
     *
     * @return self
     */
    public function setTime($time)
    {
        Assert::isInt($time);
        $this->time = (int)$time;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sets period
     *
     * @param integer $period
     *
     * @return self
     *
     * @deprecated use setTime()
     */
    public function setPeriod($period): static
    {
        Assert::isInt($period);
        $this->time = (int)$period * 3600;

        return $this;
    }

    /**
     * Gets period
     *
     * @return integer
     *
     * @deprecated use getTime()
     */
    public function getPeriod(): float|int
    {
        return $this->time / 3600;
    }
}
