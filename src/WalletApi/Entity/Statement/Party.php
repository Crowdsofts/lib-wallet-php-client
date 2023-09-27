<?php

namespace Paysera\WalletApi\Entity\Statement;

/**
 * Statement party
 */
class Party
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var string
     */
    protected $bic;

    /**
     * Gets accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Gets bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Gets code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
