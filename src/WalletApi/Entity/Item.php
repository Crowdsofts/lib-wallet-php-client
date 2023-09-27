<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing one Item in a Payment
 */
class Item
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $imageUri;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $price;

    /**
     * @var float
     */
    protected $quantity = 1;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $totalPrice;

    /**
     */
    protected $parameters;

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
     * Sets title
     *
     * @param string $title

     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Sets imageUri
     *
     * @param string $imageUri

     *
     * @return self
     */
    public function setImageUri($imageUri)
    {
        $this->imageUri = $imageUri;

        return $this;
    }

    /**
     * Gets imageUri
     *
     * @return string
     */
    public function getImageUri()
    {
        return $this->imageUri;
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
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets quantity
     *
     * @param float $quantity

     *
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Gets quantity
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets totalPrice
     *
     * @param \Paysera\WalletApi\Entity\Money $totalPrice
     *
     * @return self
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Gets totalPrice
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
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
}
