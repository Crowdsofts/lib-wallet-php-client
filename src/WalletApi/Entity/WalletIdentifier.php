<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing identifier for wallet
 */
class WalletIdentifier
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Paysera\WalletApi\Entity\Card
     */
    protected $card;

    /**
     * @var string
     */
    protected $barcode;

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
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set card
     *
     *
     * @return \Paysera\WalletApi\Entity\WalletIdentifier
     */
    public function setCard(\Paysera\WalletApi\Entity\Card $card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return \Paysera\WalletApi\Entity\Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Sets email
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets phone
     *
     * @param string $phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
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
     * Sets id
     *
     * @param integer $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $barcode
     *
     * @return $this
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Validation for wallet identifier entity
     *
     * @return boolean
     *
     * @throws \Paysera\WalletApi\Exception\LogicException
     */
    public function validate()
    {
        $setValueCount = count(
            array_diff(
                [$this->getId(), $this->getCard(), $this->getPhone(), $this->getEmail(), $this->getBarcode()],
                [null],
            ),
        );

        if ($setValueCount == 0) {
            throw new \Paysera\WalletApi\Exception\LogicException("At least one identifier must be set");
        }
        if ($setValueCount > 1) {
            throw new \Paysera\WalletApi\Exception\LogicException("Only one identifier can be set at the same time");
        }
        

        return true;
    }
}
