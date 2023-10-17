<?php

namespace Paysera\WalletApi\Entity;

/**
 * Statement
 */
class Statement
{
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_COMMISSION = 'commission';
    public const TYPE_CURRENCY = 'currency';
    public const TYPE_TAX = 'tax';
    public const TYPE_RETURN = 'return';
    public const TYPE_AUTOMATIC_PAYMENT = 'automatic_payment';

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $amount;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $details;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Paysera\WalletApi\Entity\Statement\Party
     */
    protected $otherParty;

    /**
     * @var integer
     */
    protected $transferId;

    /**
     * @var string
     */
    protected $referenceNumber;

    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets amount
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Gets direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Gets date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Gets details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Gets otherParty
     *
     * @return \Paysera\WalletApi\Entity\Statement\Party
     */
    public function getOtherParty()
    {
        return $this->otherParty;
    }

    /**
     * Gets transferId
     *
     * @return int
     */
    public function getTransferId()
    {
        return $this->transferId;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets ReferenceNumber
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }
}
