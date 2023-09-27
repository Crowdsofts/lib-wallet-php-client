<?php

namespace Paysera\WalletApi\Entity\Restriction;

class UserRestriction
{
    public const TYPE_LEGAL = 'legal';
    public const TYPE_NATURAL = 'natural';

    public const LEVEL_UNIDENTIFIED = 'unidentified';
    public const LEVEL_SEMI_IDENTIFIED = 'semi_identified';
    public const LEVEL_IDENTIFIED = 'identified';
    public const LEVEL_FULLY_IDENTIFIED = 'fully_identified';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $identityRequired;

    /**
     * @var string
     */
    private $level;

    /**
     * Creates object, used for fluent interface
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @param boolean $identityRequired
     *
     * @return $this
     */
    public function setIdentityRequired($identityRequired)
    {
        $this->identityRequired = $identityRequired;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIdentityRequired()
    {
        return $this->identityRequired;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }
}
