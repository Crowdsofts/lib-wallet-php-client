<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing one Item in a Payment
 */
class Card implements \Stringable
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * Set number
     *
     * @param string $number
     *
     * @return \Paysera\WalletApi\Entity\Card
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set issuer
     *
     * @param string $issuer
     *
     * @return \Paysera\WalletApi\Entity\Card
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return self
     */
    public static function create()
    {
        return new static();
    }

    /**
     */
    public function __toString(): string
    {
        return sprintf('%s:%s', $this->getIssuer(), $this->getNumber());
    }
}
