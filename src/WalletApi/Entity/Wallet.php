<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing Wallet
 */
class Wallet
{
    /**
     * @var integer
     *
     * @readonly
     */
    protected $id;

    /**
     * @var integer
     *
     * @readonly
     */
    protected $owner;

    /**
     * @var \Paysera\WalletApi\Entity\Wallet\Account
     *
     * @readonly
     */
    protected $account;

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
     * Gets id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets owner
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Wallet\Account
     */
    public function getAccount()
    {
        return $this->account;
    }
}
