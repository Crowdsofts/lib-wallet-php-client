<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing payment commission
 */
class Commission
{
    /**
     * @var \Paysera\WalletApi\Entity\Money $outCommission
     */
    protected $outCommission;

    /**
     * @var \Paysera\WalletApi\Entity\Money $inCommission
     */
    protected $inCommission;

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
     * Set out commission
     *
     *
     * @return self
     */
    public function setOutCommission(Money $outCommission)
    {
        $this->outCommission = $outCommission;

        return $this;
    }

    /**
     * Get out commission
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getOutCommission()
    {
        return $this->outCommission;
    }

    /**
     * Set in commission
     *
     *
     * @return self
     */
    public function setInCommission(Money $inCommission)
    {
        $this->inCommission = $inCommission;

        return $this;
    }

    /**
     * Get in commission
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getInCommission()
    {
        return $this->inCommission;
    }
}
