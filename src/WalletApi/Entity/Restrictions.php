<?php

namespace Paysera\WalletApi\Entity;

/**
 * Created by: Gediminas Samulis
 * Date: 2013-12-18
 */

class Restrictions
{
    /**
     * @var \Paysera\WalletApi\Entity\Restriction\UserRestriction
     */
    protected $accountOwnerRestriction;

    /**
     * Creates object, used for fluent interface
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @param \\Paysera\WalletApi\Entity\Restriction\UserRestriction $accountOwnerRestriction
     *
     * @return $this
     */
    public function setAccountOwnerRestriction($accountOwnerRestriction)
    {
        $this->accountOwnerRestriction = $accountOwnerRestriction;

        return $this;
    }

    /**
     * @return \\Paysera\WalletApi\Entity\Restriction\UserRestriction
     */
    public function getAccountOwnerRestriction()
    {
        return $this->accountOwnerRestriction;
    }
}
