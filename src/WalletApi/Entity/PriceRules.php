<?php

namespace Paysera\WalletApi\Entity;

/**
 * PriceRules
 */
class PriceRules
{
    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $min;

    /**
     * @var \Paysera\WalletApi\Entity\Money
     */
    protected $max;

    /**
     * @var \Paysera\WalletApi\Entity\Money[]
     */
    protected $choices = [];

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
     * @param \Paysera\WalletApi\Entity\Money $min
     *
     * @return self
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Money $max
     *
     * @return self
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Money $choice
     *
     * @return self
     */
    public function addChoice($choice)
    {
        $this->choices[] = $choice;

        return $this;
    }

    /**
     * @param \Paysera\WalletApi\Entity\Money[] $choices
     *
     * @return self
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Money[]
     */
    public function getChoices()
    {
        return $this->choices;
    }
}
