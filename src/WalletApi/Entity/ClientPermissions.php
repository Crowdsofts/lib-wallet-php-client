<?php

namespace Paysera\WalletApi\Entity;

/**
 * \Paysera\WalletApi\Entity\ClientPermissions
 */
class ClientPermissions
{
    /**
     * Scopes
     */
    public const SCOPE_SHOW_IN_FRAME = 'show_in_frame';
    public const SCOPE_GIVE_TRUSTED_USER_INFO = 'give_trusted_user_info';
    public const SCOPE_USE_PASSWORD_GRANT = 'use_password_grant';
    public const SCOPE_ACCEPT_WITH_FLASH = 'accept_with_flash';
    public const SCOPE_ACCEPT_WITH_PIN = 'accept_with_pin';
    public const SCOPE_ACCESS_STATEMENTS = 'access_statements';
    public const SCOPE_SEARCH_BY_PERSON_CODE = 'search_by_person_code';
    public const SCOPE_USE_WALLET_API = 'use_wallet_api';
    public const SCOPE_USE_CLIENT_API = 'use_client_api';
    public const SCOPE_USE_OAUTH_API = 'use_oauth_api';
    public const SCOPE_PROVIDE_REGISTRATION_PARAMETERS = 'provide_registration_parameters';

    /**
     * @var array
     */
    protected $scopes = [];

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Set scopes
     *
     * @param array $scopes
     *
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Get scopes
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isShowInFrameGranted()
    {
        return $this->isGranted(self::SCOPE_SHOW_IN_FRAME);
    }

    /**
     * @return bool
     */
    public function isGiveTrustedUserInfoGranted()
    {
        return $this->isGranted(self::SCOPE_GIVE_TRUSTED_USER_INFO);
    }

    /**
     * @return bool
     */
    public function isUsePasswordGrantGranted()
    {
        return $this->isGranted(self::SCOPE_USE_PASSWORD_GRANT);
    }

    /**
     * @return bool
     */
    public function isAcceptWithFlashGranted()
    {
        return $this->isGranted(self::SCOPE_ACCEPT_WITH_FLASH);
    }

    /**
     * @return bool
     */
    public function isAcceptWithPinGranted()
    {
        return $this->isGranted(self::SCOPE_ACCEPT_WITH_PIN);
    }

    /**
     * @return bool
     */
    public function isUseWalletApiGranted()
    {
        return $this->isGranted(self::SCOPE_USE_WALLET_API);
    }

    /**
     * @return bool
     */
    public function isUseOAuthApiGranted()
    {
        return $this->isGranted(self::SCOPE_USE_OAUTH_API);
    }

    /**
     * @return bool
     */
    public function isProvideRegistrationParametersGranted()
    {
        return $this->isGranted(self::SCOPE_PROVIDE_REGISTRATION_PARAMETERS);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantShowInFrame()
    {
        return $this->grant(self::SCOPE_SHOW_IN_FRAME);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantGiveTrustedUserInfo()
    {
        return $this->grant(self::SCOPE_GIVE_TRUSTED_USER_INFO);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantUsePasswordGrant()
    {
        return $this->grant(self::SCOPE_USE_PASSWORD_GRANT);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantAcceptWithFlash()
    {
        return $this->grant(self::SCOPE_ACCEPT_WITH_FLASH);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantAcceptWithPin()
    {
        return $this->grant(self::SCOPE_ACCEPT_WITH_PIN);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantAccessStatements()
    {
        return $this->grant(self::SCOPE_ACCESS_STATEMENTS);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function grantSearchByPersonCode()
    {
        return $this->grant(self::SCOPE_SEARCH_BY_PERSON_CODE);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeShowInFrame()
    {
        return $this->revoke(self::SCOPE_SHOW_IN_FRAME);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeGiveTrustedUserInfo()
    {
        return $this->revoke(self::SCOPE_GIVE_TRUSTED_USER_INFO);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeUsePasswordGrant()
    {
        return $this->revoke(self::SCOPE_USE_PASSWORD_GRANT);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeAcceptWithFlash()
    {
        return $this->revoke(self::SCOPE_ACCEPT_WITH_FLASH);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeAcceptWithPin()
    {
        return $this->revoke(self::SCOPE_ACCEPT_WITH_PIN);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeAccessStatements()
    {
        return $this->revoke(self::SCOPE_ACCESS_STATEMENTS);
    }

    /**
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    public function revokeSearchByPersonCode()
    {
        return $this->revoke(self::SCOPE_SEARCH_BY_PERSON_CODE);
    }

    /**
     * Method to check if scope is granted
     *
     * @param string $scope
     *
     * @return bool
     */
    public function isGranted($scope)
    {
        return in_array($scope, $this->scopes);
    }

    /**
     * Revoke
     *
     * @param string $scope
     *
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    protected function revoke($scope)
    {
        $index = array_search($scope, $this->scopes);

        if ($index !== false) {
            unset($this->scopes[$index]);
        }

        return $this;
    }

    /**
     * Grant
     *
     * @param string $scope
     *
     * @return \Paysera\WalletApi\Entity\ClientPermissions
     */
    protected function grant($scope)
    {
        if (!$this->isGranted($scope)) {
            $this->scopes[] = $scope;
        }

        return $this;
    }
}
