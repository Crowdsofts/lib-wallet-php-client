<?php

namespace Paysera\WalletApi\Entity;

use Paysera\WalletApi\Exception\LogicException;
use Paysera\WalletApi\Util\Assert;

/**
 * Entity representing Transaction
 */
class Transaction
{
    public const STATUS_NEW = 'new';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_WAITING_REGISTRATION = 'waiting_registration';
    public const STATUS_WAITING_FUNDS = 'waiting_funds';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVOKED = 'revoked';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CONFIRMED = 'confirmed';

    public const TYPE_AUTOMATIC = 'automatic';
    public const TYPE_PAGE = 'page';
    public const TYPE_FLASH = 'flash';
    public const TYPE_PIN = 'pin';

    /**
     * @var string    read-only
     */
    protected $key;

    /**
     * @var \DateTime    read-only
     */
    protected $createdAt;

    /**
     * @var string    read-only
     */
    protected $status;

    /**
     * @var string    read-only
     */
    protected $type;

    /**
     * @var integer    read-only
     */
    protected $wallet;

    /**
     * @var \DateTime    read-only
     */
    protected $confirmedAt;

    /**
     * @var string    read-only
     */
    protected $correlationKey;

    /**
     * @var \Paysera\WalletApi\Entity\Payment[]
     */
    protected $payments = [];

    /**
     * @var integer[]
     */
    protected $paymentIdList = [];

    /**
     * @var Allowance
     */
    protected $allowance;

    /**
     * @var integer
     */
    protected $allowanceId;

    /**
     * @var boolean
     */
    protected $allowanceOptional = false;

    /**
     * @var boolean
     */
    protected $useAllowance = true;

    /**
     * @var boolean
     */
    protected $suggestAllowance = false;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var string
     */
    protected $callbackUri;

    /**
     * @var boolean
     */
    protected $callbacksDisabled = false;

    /**
     * @var integer
     */
    protected $reserveFor;

    /**
     * @var \DateTime
     */
    protected $reserveUntil;

    /**
     * @var \Paysera\WalletApi\Entity\UserInformation
     */
    protected $userInformation;

    /**
     * @var boolean
     */
    protected $autoConfirm;

    /**
     * @var \Paysera\WalletApi\Entity\Restrictions
     */
    protected $restrictions;

    /**
     * @var integer $locationId
     */
    protected $locationId;

    /**
     * @var int|null
     */
    protected $managerId;

    /**
     * @var \Paysera\WalletApi\Entity\Inquiry\Inquiry[]
     */
    protected $inquiries;

    public function __construct()
    {
        $this->inquiries = [];
    }

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
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get status
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
    public function isStatusWaitingFunds()
    {
        return $this->status === self::STATUS_WAITING_FUNDS;
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypeAutomatic()
    {
        return $this->type === self::TYPE_AUTOMATIC;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypeFlash()
    {
        return $this->type === self::TYPE_FLASH;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypePage()
    {
        return $this->type === self::TYPE_PAGE;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypePin()
    {
        return $this->type === self::TYPE_PIN;
    }

    /**
     * Sets wallet
     *
     * @param integer $wallet

     *
     * @return self
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
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
     * Get confirmedAt
     *
     * @return DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Get correlationKey
     *
     * @return string
     */
    public function getCorrelationKey()
    {
        return $this->correlationKey;
    }

    /**
     * Add payment
     *
     *
     * @return self
     *
     * @throws LogicException
     */
    public function addPayment(Payment $payment)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot add payment to already saved transaction');
        }
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Get payments
     *
     * @return \Paysera\WalletApi\Entity\Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Adds payment to transaction by it's ID
     *
     * @param integer $paymentId
     *
     * @return self
     *
     * @throws LogicException
     */
    public function addPaymentId($paymentId)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot add payment to already saved transaction');
        }
        Assert::isInt($paymentId);
        $this->paymentIdList[] = $paymentId;

        return $this;
    }

    /**
     * Gets paymentIdList
     *
     * @return integer[]
     */
    public function getPaymentIdList()
    {
        return $this->paymentIdList;
    }

    /**
     * Set allowance
     *
     *
     * @return self
     *
     * @throws LogicException
     */
    public function setAllowance(Allowance $allowance)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot set allowance to already saved transaction');
        }
        $this->allowance = $allowance;
        $this->allowanceId = null;

        return $this;
    }

    /**
     * Get allowance
     *
     * @return Allowance
     */
    public function getAllowance()
    {
        return $this->allowance;
    }

    /**
     * Sets allowance by it's ID
     *
     * @param integer $allowanceId
     *
     * @return self
     *
     * @throws LogicException
     */
    public function setAllowanceId($allowanceId)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot set allowance to already saved transaction');
        }
        \Paysera\WalletApi\Util\Assert::isInt($allowanceId);
        $this->allowanceId = $allowanceId;
        $this->allowance = null;

        return $this;
    }

    /**
     * Gets allowanceId
     *
     * @return integer
     */
    public function getAllowanceId()
    {
        return $this->allowanceId;
    }

    /**
     * Set allowanceOptional
     *
     * @param boolean $allowanceOptional
     *
     * @return self
     */
    public function setAllowanceOptional($allowanceOptional)
    {
        $this->allowanceOptional = (bool)$allowanceOptional;

        return $this;
    }

    /**
     * Get allowanceOptional
     *
     * @return boolean
     */
    public function getAllowanceOptional()
    {
        return $this->allowanceOptional;
    }

    /**
     * Get allowanceOptional
     *
     * @return boolean
     */
    public function isAllowanceOptional()
    {
        return $this->allowanceOptional;
    }

    /**
     * Set useAllowance
     *
     * @param boolean $useAllowance
     *
     * @return self
     */
    public function setUseAllowance($useAllowance)
    {
        $this->useAllowance = (bool)$useAllowance;

        return $this;
    }

    /**
     * Get useAllowance
     *
     * @return boolean
     */
    public function getUseAllowance()
    {
        return $this->useAllowance;
    }

    /**
     * Gets useAllowance
     *
     * @return boolean
     */
    public function isUseAllowance()
    {
        return $this->useAllowance;
    }

    /**
     * Set suggestAllowance
     *
     * @param boolean $suggestAllowance
     *
     * @return self
     */
    public function setSuggestAllowance($suggestAllowance)
    {
        $this->suggestAllowance = (bool)$suggestAllowance;

        return $this;
    }

    /**
     * Get suggestAllowance
     *
     * @return boolean
     */
    public function getSuggestAllowance()
    {
        return $this->suggestAllowance;
    }

    /**
     * Gets suggestAllowance
     *
     * @return boolean
     */
    public function isSuggestAllowance()
    {
        return $this->suggestAllowance;
    }

    /**
     * Set redirectUri
     *
     * @param string $redirectUri
     *
     * @return self
     */
    public function setRedirectUri($redirectUri)
    {
        if ($redirectUri === null) {
            $this->redirectUri = null;
        } else {
            \Paysera\WalletApi\Util\Assert::isScalar($redirectUri);
            $this->redirectUri = (string)$redirectUri;
        }

        return $this;
    }

    /**
     * Get redirectUri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Set callbackUri
     *
     * @param string|boolean false $callbackUri
     *
     * @return self
     */
    public function setCallbackUri($callbackUri)
    {
        if ($callbackUri === null) {
            $this->setCallbacksDisabled(false);
            $this->callbackUri = null;
        } elseif ($callbackUri === false) {
            $this->disableCallbacks();
        } else {
            \Paysera\WalletApi\Util\Assert::isScalar($callbackUri);
            $this->callbackUri = (string)$callbackUri;
            $this->setCallbacksDisabled(false);
        }

        return $this;
    }

    /**
     * Get callbackUri
     *
     * @return string
     */
    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    /**
     * Set callbacksDisabled
     *
     * @param boolean $callbacksDisabled
     *
     * @return self
     */
    public function setCallbacksDisabled($callbacksDisabled)
    {
        $this->callbacksDisabled = (bool)$callbacksDisabled;
        if ($this->callbacksDisabled) {
            $this->callbackUri = null;
        }

        return $this;
    }

    /**
     * Set callbacksDisabled to true
     *
     * @return self
     */
    public function disableCallbacks()
    {
        $this->setCallbacksDisabled(true);

        return $this;
    }

    /**
     * Get callbacksDisabled
     *
     * @return boolean
     */
    public function getCallbacksDisabled()
    {
        return $this->callbacksDisabled;
    }

    /**
     * Get callbacksDisabled
     *
     * @return boolean
     */
    public function isCallbacksDisabled()
    {
        return $this->callbacksDisabled;
    }

    /**
     * Gets reserve period in hours.
     *
     * @return integer
     */
    public function getReserveFor()
    {
        return $this->reserveFor;
    }

    /**
     * Sets reserve period in hours
     *
     * @param integer $reserveForInHours
     *
 * @return self
     *
     * @throws LogicException
     */
    public function setReserveFor($reserveForInHours)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot change reserve time to already saved transaction');
        }
        $this->reserveFor = $reserveForInHours;
        $this->reserveUntil = null;

        return $this;
    }

    /**
     * Gets reserveUntil
     *
     * @return DateTime
     */
    public function getReserveUntil()
    {
        return $this->reserveUntil;
    }

    /**
     * Sets reserveUntil
     *
     *
     * @return self
     *
     * @throws LogicException
     */
    public function setReserveUntil(\DateTime $reserveUntil)
    {
        if ($this->getKey() !== null) {
            throw new LogicException('Cannot change reserve time to already saved transaction');
        }
        $this->reserveFor = null;
        $this->reserveUntil = $reserveUntil;

        return $this;
    }

    /**
     * Gets userInformation
     *
     * @return \Paysera\WalletApi\Entity\UserInformation
     */
    public function getUserInformation()
    {
        return $this->userInformation;
    }

    /**
     * Sets userInformation
     *
     * @return self
     */
    public function setUserInformation(\Paysera\WalletApi\Entity\UserInformation $userInformation)
    {
        $this->userInformation = $userInformation;

        return $this;
    }

    /**
     * Sets autoConfirm
     *
     * @param boolean $autoConfirm
     *
     * @return self
     */
    public function setAutoConfirm($autoConfirm)
    {
        $this->autoConfirm = $autoConfirm;

        return $this;
    }

    /**
     * Gets autoConfirm
     *
     * @return boolean
     */
    public function isAutoConfirm()
    {
        return $this->autoConfirm;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Restrictions $restrictions
     *
     * @return $this
     */
    public function setRestrictions($restrictions)
    {
        $this->restrictions = $restrictions;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Restrictions
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param integer $locationId
     *
     * @return $this
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * Id of the user who created this transaction.
     *
     * @return int|null
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Inquiry\Inquiry[]
     */
    public function getInquiries()
    {
        return $this->inquiries;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Inquiry\Inquiry[] $inquiries
     *
     * @return $this
     */
    public function setInquiries($inquiries)
    {
        $this->inquiries = $inquiries;

        return $this;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Inquiry\Inquiry $inquiry
     *
     * @return $this
     */
    public function addInquiry($inquiry)
    {
        $this->inquiries[] = $inquiry;

        return $this;
    }
}
