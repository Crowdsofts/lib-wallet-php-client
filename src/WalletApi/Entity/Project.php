<?php

namespace Paysera\WalletApi\Entity;

/**
 * Entity representing Project
 */
class Project
{
    /**
     * @var integer
     *
     * @readonly
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var integer
     */
    protected $walletId;

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
     * Set id
     *
     * @param int $id
     *
     * @return \Paysera\WalletApi\Entity\Project
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \Paysera\WalletApi\Entity\Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return \Paysera\WalletApi\Entity\Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set walletId
     *
     * @param int $walletId
     *
     * @return \Paysera\WalletApi\Entity\Project
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;

        return $this;
    }

    /**
     * Get walletId
     *
     * @return int
     */
    public function getWalletId()
    {
        return $this->walletId;
    }
}
