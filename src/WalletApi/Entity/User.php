<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing User
 */
class User
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const TYPE_NATURAL = 'natural';
    public const TYPE_LEGAL = 'legal';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var \Paysera\WalletApi\Entity\User\Address
     */
    protected $address;

    /**
     * @var \Paysera\WalletApi\Entity\User\Identity
     */
    protected $identity;

    /**
     * @var int[]
     */
    protected $wallets;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $dob;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $companyCode;

    /**
     * @var string
     */
    protected $identificationLevel;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $politicallyExposedPersons;

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
     * @return \Paysera\WalletApi\Entity\User\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return int[]
     */
    public function getWallets()
    {
        return $this->wallets;
    }

    /**
     * Setter of Id
     *
     * @param int $id
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Setter of Address
     *
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function setAddress(\Paysera\WalletApi\Entity\User\Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Setter of Email
     *
     * @param string $email
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Setter of Identity
     *
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function setIdentity(\Paysera\WalletApi\Entity\User\Identity $identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * Setter of Phone
     *
     * @param string $phone
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets dob
     *
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Gets gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Returns whether the user is a natural or a legal entity.
     *
     * @return string natural|legal
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * Returns null if gender is unknown, true/false if gender is known
     *
     * @return bool|null
     */
    public function isGenderMale()
    {
        return $this->gender !== null ? $this->gender === self::GENDER_MALE : null;
    }

    /**
     * Returns null if gender is unknown, true/false if gender is known
     *
     * @return bool|null
     */
    public function isGenderFemale()
    {
        return $this->gender !== null ? $this->gender === self::GENDER_FEMALE : null;
    }

    /**
     * @param string $identificationLevel
     *
     * @return $this
     */
    public function setIdentificationLevel($identificationLevel)
    {
        $this->identificationLevel = $identificationLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationLevel()
    {
        return $this->identificationLevel;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\User\PoliticallyExposedPerson[]
     */
    public function getPoliticallyExposedPersons()
    {
        return $this->politicallyExposedPersons;
    }
}
