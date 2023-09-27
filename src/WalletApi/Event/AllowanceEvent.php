<?php

namespace Paysera\WalletApi\Event;

/**
 * AllowanceEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class AllowanceEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Entity\Allowance
     */
    protected $allowance;

    public function __construct(\Paysera\WalletApi\Entity\Allowance $allowance)
    {
        $this->allowance = $allowance;
    }

    /**
     * Gets allowance
     *
     * @return \Paysera\WalletApi\Entity\Allowance
     */
    public function getAllowance()
    {
        return $this->allowance;
    }
}
