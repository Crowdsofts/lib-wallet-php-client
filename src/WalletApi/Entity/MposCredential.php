<?php

namespace Paysera\WalletApi\Entity;

class MposCredential
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $projectId;

    /**
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }
}
