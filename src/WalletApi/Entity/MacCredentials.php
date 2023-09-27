<?php

namespace Paysera\WalletApi\Entity;

/**
 * Credentials
 */
class MacCredentials
{
    /**
     * @var string
     */
    protected $macId;

    /**
     * @var string
     */
    protected $macKey;

    /**
     * @var string
     */
    protected $algorithm;

    /**
     * @return \Paysera\WalletApi\Entity\MacCredentials
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Set algorithm
     *
     * @param string $algorithm
     *
     * @return \Paysera\WalletApi\Entity\MacCredentials
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get algorithm
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Set macId
     *
     * @param string $macId
     *
     * @return \Paysera\WalletApi\Entity\MacCredentials
     */
    public function setMacId($macId)
    {
        $this->macId = $macId;

        return $this;
    }

    /**
     * Get macId
     *
     * @return string
     */
    public function getMacId()
    {
        return $this->macId;
    }

    /**
     * Set macKey
     *
     * @param string $macKey
     *
     * @return \Paysera\WalletApi\Entity\MacCredentials
     */
    public function setMacKey($macKey)
    {
        $this->macKey = $macKey;

        return $this;
    }

    /**
     * Get macKey
     *
     * @return string
     */
    public function getMacKey()
    {
        return $this->macKey;
    }
}
