<?php

namespace Paysera\WalletApi\Entity;

use Paysera\WalletApi\Entity\Client\Host;

/**
 * Entity representing Client
 */
class Client
{
    public const TYPE_PRIVATE_CLIENT = 'private_client';
    public const TYPE_APPLICATION = 'application';
    public const TYPE_APP_CLIENT = 'app_client';

    /**
     */
    protected int $id;

    /**
     * @readonly
     */
    protected string $title;

    /**
     */
    protected ClientPermissions $permissions;

    /**
     * @var Host[]
     */
    protected array $hosts = [];

    /**
     */
    protected string $type;

    /**
     */
    protected Project $mainProject;

    /**
     */
    protected int $mainProjectId;

    /**
     */
    protected int $serviceAgreementId;

    /**
     */
    protected MacCredentials $credentials;

    public function __construct()
    {
        $this->permissions = new ClientPermissions();
    }

    /**
     * @return self
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return Client
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set permissions
     *
     *
     * @return Client
     */
    public function setPermissions(ClientPermissions $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return \ClientPermissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set mainProject
     *
     * @param \\Paysera\WalletApi\Entity\Project $mainProject
     *
     * @return Client
     */
    public function setMainProject($mainProject)
    {
        $this->mainProject = $mainProject;

        return $this;
    }

    /**
     * Get mainProject
     *
     * @return \\Paysera\WalletApi\Entity\Project
     */
    public function getMainProject()
    {
        return $this->mainProject;
    }

    /**
     * Set mainProjectId
     *
     * @param int $mainProjectId
     *
     * @return Client
     */
    public function setMainProjectId($mainProjectId)
    {
        $this->mainProjectId = $mainProjectId;

        return $this;
    }

    /**
     * Get mainProjectId
     *
     * @return int
     */
    public function getMainProjectId()
    {
        return $this->mainProjectId;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Client
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set hosts
     *
     * @param Client\Host[] $hosts
     *
     * @return Client
     */
    public function setHosts(array $hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * Get hosts
     *
     * @return Client\Host[]
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * Set credentials
     *
     *
     * @return Client
     */
    public function setCredentials(MacCredentials $credentials): static
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * Get credentials
     *
     */
    public function getCredentials(): MacCredentials
    {
        return $this->credentials;
    }

    /**
     * @return int
     */
    public function getServiceAgreementId()
    {
        return $this->serviceAgreementId;
    }

    /**
     * @param int $serviceAgreementId
     *
     * @return $this
     */
    public function setServiceAgreementId($serviceAgreementId)
    {
        $this->serviceAgreementId = $serviceAgreementId;

        return $this;
    }
}
