<?php

namespace Paysera\WalletApi\Entity\User;

/**
 * Identity
 */
class Identity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $surname;

    /**
     * @var string
     */
    protected $nationality;

    /**
     * @var string
     */
    protected $code;

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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Setter of Code
     *
     * @param string $code
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Setter of Name
     *
     * @param string $name
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Setter of Nationality
     *
     * @param string $nationality
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Setter of Surname
     *
     * @param string $surname
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }
}
