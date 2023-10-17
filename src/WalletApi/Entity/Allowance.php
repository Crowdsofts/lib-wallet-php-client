<?php

namespace Paysera\WalletApi\Entity;

use Paysera\WalletApi\Util\Assert;

/**
 * Entity representing Allowance
 */
class Allowance
{
    public const STATUS_NEW = 'new';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DELETED = 'deleted';

    /**
     * @var integer     read-only
     */
    protected $id;

    /**
     * @var string      read-only
     */
    protected $transactionKey;

    /**
     * @var \DateTime     read-only
     */
    protected $createdAt;

    /**
     * @var string      read-only
     */
    protected $status;

    /**
     * @var integer     read-only
     */
    protected $wallet;

    /**
     * @var \DateTime     read-only
     */
    protected $confirmedAt;

    /**
     * @var \DateTime
     */
    protected $validUntil;

    /**
     * @var integer
     */
    protected $validFor;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $maxPrice;

    /**
     * @var \Paysera\WalletApi\Entity\Limit[]
     */
    protected $limits = [];

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
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Checks allowance status
     *
     * @return boolean
     */
    public function isStatusNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Checks allowance status
     *
     * @return boolean
     */
    public function isStatusActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Checks allowance status
     *
     * @return boolean
     */
    public function isStatusInactive()
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Checks allowance status
     *
     * @return boolean
     */
    public function isStatusDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    /**
     * Sets validUntil
     *
     * @return self
     */
    public function setValidUntil(\DateTime  $validUntil)
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    /**
     * Gets validUntil
     *
     * @return \DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * Gets confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Sets description
     *
     * @param string $description

     *
     * @return self
     */
    public function setDescription($description)
    {
        Assert::isScalar($description);
        $this->description = (string)$description;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets max price
     *
     * @return self
     */
    public function setMaxPrice(Money $price)
    {
        $this->maxPrice = $price;

        return $this;
    }

    /**
     * Gets max price
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * Sets limits
     *
     * @param \Paysera\WalletApi\Entity\Limit[] $limits

     *
     * @return self
     */
    public function setLimits(array $limits)
    {
        $this->limits = [];
        foreach ($limits as $limit) {
            $this->addLimit($limit);
        }

        return $this;
    }

    /**
     * Gets limits
     *
     * @return \Paysera\WalletApi\Entity\Limit[]
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * Adds limit
     *
     *
     * @return self
     */
    public function addLimit(Limit $limit)
    {
        $this->limits[] = $limit;

        return $this;
    }

    /**
     * Returns whether this allowance has any limits
     *
     * @return boolean
     */
    public function hasLimits()
    {
        return count($this->limits) > 0;
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
     * Gets transactionKey
     *
     * @return string
     */
    public function getTransactionKey()
    {
        return $this->transactionKey;
    }

    /**
     * Gets createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Gets wallet
     *
     * @return integer
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * Gets validFor
     *
     * @return integer
     */
    public function getValidFor()
    {
        return $this->validFor;
    }

    /**
     * Sets validFor
     *
     * @param integer $validFor

     *
     * @return self
     */
    public function setValidFor($validFor)
    {
        Assert::isIntOrNull($validFor);
        $this->validFor = $validFor;

        return $this;
    }

    public function getEffectiveValidUntil(): \DateTime
    {
        if ($this->validUntil !== null) {
            return $this->validUntil;
        }

        return (new \DateTime())->add(new \DateInterval('PT' . $this->validFor . 'H'));
    }
}
