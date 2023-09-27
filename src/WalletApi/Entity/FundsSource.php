<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing payment funds source
 */
class FundsSource
{
    public const TYPE_BUY = 'buy';
    public const TYPE_SELL = 'sell';
    public const TYPE_LOAN = 'loan';
    public const TYPE_GIFT = 'gift';
    public const TYPE_INHERITANCE = 'inheritance';
    public const TYPE_DIVIDENDS = 'dividends';

    protected static $types = [self::TYPE_BUY => self::TYPE_BUY, self::TYPE_SELL => self::TYPE_SELL, self::TYPE_LOAN => self::TYPE_LOAN, self::TYPE_GIFT => self::TYPE_GIFT, self::TYPE_INHERITANCE => self::TYPE_INHERITANCE, self::TYPE_DIVIDENDS => self::TYPE_DIVIDENDS];

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $details
     */
    protected $details;

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
     * Set type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set details
     *
     * @param string $details
     *
     * @return self
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Get available funds source types
     *
     * @return array
     */
    public static function getAvailableTypes()
    {
        return self::$types;
    }
}
