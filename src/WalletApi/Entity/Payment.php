<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing Payment
 */
class Payment
{
    public const STATUS_NEW = 'new';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_WAITING_REGISTRATION = 'waiting_registration';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVOKED = 'revoked';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELED = 'canceled';

    public const PURPOSE_CASH = 'cash';
    public const PURPOSE_TIPS = 'tips';

    /**
     * @var integer    read-only
     */
    protected $id;

    /**
     * @var string    read-only
     */
    protected $status;

    /**
     * @var string    read-only
     */
    protected $transactionKey;

    /**
     * @var \DateTime    read-only
     */
    protected $createdAt;

    /**
     * @var integer    read-only
     */
    protected $walletId;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Item[]
     */
    protected $items = [];

    /**
     * @var WalletIdentifier
     */
    protected $beneficiary;

    /**
     * @var integer
     */
    protected $freezeFor;

    /**
     * @var \DateTime
     */
    protected $freezeUntil;

    /**
     */
    protected $parameters;

    /**
     * @var \DateTime    read-only
     */
    protected $confirmedAt;

    /**
     * @var Money
     */
    protected $price;

    /**
     * @var Commission
     */
    protected $commission;

    /**
     * @var Money
     */
    protected $cashback;

    /**
     * @var PaymentPassword
     */
    protected $paymentPassword;

    /**
     * @var string
     */
    protected $purpose;

    /**
     * @var PriceRules
     */
    protected $priceRules;

    /**
     * @var FundsSource
     */
    protected $fundsSource;

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
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusWaiting()
    {
        return $this->status === self::STATUS_WAITING;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusWaitingRegistration()
    {
        return $this->status === self::STATUS_WAITING_REGISTRATION;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusRevoked()
    {
        return $this->status === self::STATUS_REVOKED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    /**
     * Checks status
     *
     * @return boolean
     */
    public function isStatusDone()
    {
        return $this->status === self::STATUS_DONE;
    }

    /**
     * Checks status
     *
     * @return boolean
     */
    public function isStatusCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
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
     * Gets walletId
     *
     * @return integer
     */
    public function getWalletId()
    {
        return $this->walletId;
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
        $this->description = $description;

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
     * Sets price
     *
     * @return self
     */
    public function setPrice(Money $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets price
     *
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets commission
     *
     *
     * @return self
     */
    public function setCommission(Commission $commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Gets commission
     *
     * @return Commission
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Sets cashback
     *
     * @param Money $cashback
     *
     * @return self
     */
    public function setCashback($cashback)
    {
        $this->cashback = $cashback;

        return $this;
    }

    /**
     * Gets cashback
     *
     * @return Money
     */
    public function getCashback()
    {
        return $this->cashback;
    }

    /**
     * Sets items
     *
     * @param Item[] $items

     *
     * @return self
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Gets items
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Adds item
     *
     *
     * @return self
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Checks if this payment has any items
     *
     * @return boolean
     */
    public function hasItems()
    {
        return count($this->items) > 0;
    }

    /**
     * Sets beneficiary
     *
     * @param WalletIdentifier|null $beneficiary

     *
     * @return self
     */
    public function setBeneficiary($beneficiary)
    {
        $this->beneficiary = $beneficiary;

        return $this;
    }

    /**
     * Gets beneficiary
     *
     * @return WalletIdentifier
     */
    public function getBeneficiary()
    {
        return $this->beneficiary;
    }

    /**
     * Sets parameters
     *
     * @return self
     */
    public function setParameters(mixed $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets parameters
     *
     */
    public function getParameters()
    {
        return $this->parameters;
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
     * Sets freeze period in hours
     *
     * @param integer $freezeForInHours

     *
     * @return self
     */
    public function setFreezeFor($freezeForInHours)
    {
        $this->freezeFor = $freezeForInHours;
        $this->freezeUntil = null;

        return $this;
    }

    /**
     * Gets freeze period in hours.
     *
     * @return integer
     */
    public function getFreezeFor()
    {
        return $this->freezeFor;
    }

    /**
     * Gets freezeUntil
     *
     * @return \DateTime
     */
    public function getFreezeUntil()
    {
        return $this->freezeUntil;
    }

    /**
     * Sets freezeUntil
     *
     *
     * @return self
     */
    public function setFreezeUntil(\DateTime $freezeUntil)
    {
        $this->freezeFor = null;
        $this->freezeUntil = $freezeUntil;

        return $this;
    }

    /**
     * Set paymentPassword
     *
     * @param PaymentPassword $paymentPassword
     *
     * @return self
     */
    public function setPaymentPassword($paymentPassword)
    {
        $this->paymentPassword = $paymentPassword;

        return $this;
    }

    /**
     * @return PaymentPassword
     */
    public function getPaymentPassword()
    {
        return $this->paymentPassword;
    }

    /**
     * Sets priceRules
     *
     * @param PriceRules $priceRules
     *
     * @return self
     */
    public function setPriceRules($priceRules)
    {
        $this->priceRules = $priceRules;

        return $this;
    }

    /**
     * Gets priceRules
     *
     * @return PriceRules
     */
    public function getPriceRules()
    {
        return $this->priceRules;
    }

    /**
     * Sets purpose
     *
     * @param string $purpose
     *
     * @return $this
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Gets purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * Set funds source
     *
     * @param FundsSource $fundsSource
     *
     * @return $this
     */
    public function setFundsSource($fundsSource)
    {
        $this->fundsSource = $fundsSource;

        return $this;
    }

    /**
     * Get funds source
     *
     * @return FundsSource
     */
    public function getFundsSource()
    {
        return $this->fundsSource;
    }
}
