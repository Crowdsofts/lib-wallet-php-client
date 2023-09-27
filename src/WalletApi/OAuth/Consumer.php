<?php

namespace Paysera\WalletApi\OAuth;

use Paysera\WalletApi\Exception\OAuthException;

class Consumer
{
    public const SCOPE_BALANCE = 'balance';
    public const SCOPE_BALANCE_OFFLINE = 'balance_offline';
    public const SCOPE_STATEMENTS = 'statements';
    public const SCOPE_RECENT_STATEMENTS = 'recent_statements';
    public const SCOPE_STATEMENTS_OFFLINE = 'statements_offline';
    public const SCOPE_PHONE_CONFIRMATION = 'phone_confirmation';
    public const SCOPE_PHONE_CONFIRMATION_OFFLINE = 'phone_confirmation_offline';
    public const SCOPE_PHONE = 'phone';
    public const SCOPE_PHONE_OFFLINE = 'phone_offline';
    public const SCOPE_EMAIL = 'email';
    public const SCOPE_EMAIL_OFFLINE = 'email_offline';
    public const SCOPE_ADDRESS = 'address';
    public const SCOPE_ADDRESS_OFFLINE = 'address_offline';
    public const SCOPE_IDENTITY = 'identity';
    public const SCOPE_IDENTITY_OFFLINE = 'identity_offline';
    public const SCOPE_FULL_NAME = 'full_name';
    public const SCOPE_FULL_NAME_OFFLINE = 'full_name_offline';
    public const SCOPE_IDENTIFICATION_LEVEL = 'identification_level';
    public const SCOPE_IDENTIFICATION_LEVEL_OFFLINE = 'identification_level_offline';
    public const SCOPE_WALLET_LIST = 'wallet_list';
    public const SCOPE_WALLET_LIST_OFFLINE = 'wallet_list_offline';
    public const SCOPE_DOB = 'dob';
    public const SCOPE_DOB_OFFLINE = 'dob_offline';
    public const SCOPE_GENDER = 'gender';
    public const SCOPE_GENDER_OFFLINE = 'gender_offline';
    public const SCOPE_USER_INFO = 'user_info';
    public const SCOPE_IDENTIFICATION_DATA = 'identification_data';
    public const SCOPE_IDENTIFICATION_DATA_OFFLINE = 'identification_data_offline';
    public const SCOPE_INITIATE_TRANSFERS = 'initiate_transfers';
    public const SCOPE_CHECK_HAS_SUFFICIENT_BALANCE = 'check_has_sufficient_balance';
    public const SCOPE_PEP = 'pep';

    /**
     * @var array all query parameters which can be used in OAuth authentication
     */
    protected static $authenticationParameters = ['error', 'error_description', 'state', 'code'];

    /**
     * @var \Paysera\WalletApi\State\StatePersisterInterface
     */
    protected $statePersister;

    /**
     * @var \Paysera\WalletApi\Util\Router
     */
    protected $router;

    /**
     * @var \Paysera\WalletApi\Util\RequestInfo
     */
    protected $requestInfo;

    /**
     * @var \Paysera\WalletApi\Client\OAuthClient
     */
    protected $oauthClient;

    /**
     * Constructs object
     *
     * @param string $clientId
     */
    public function __construct(
        protected $clientId,
        \Paysera\WalletApi\Client\OAuthClient $oauthClient,
        \Paysera\WalletApi\Util\Router $router,
        \Paysera\WalletApi\State\StatePersisterInterface $statePersister,
        \Paysera\WalletApi\Util\RequestInfo $requestInfo
    ) {
        $this->oauthClient = $oauthClient;
        $this->router = $router;
        $this->statePersister = $statePersister;
        $this->requestInfo = $requestInfo;
    }

    /**
     * Gets redirect URI for OAuth authorization. After confirming or rejecting authorization request, user will
     * be redirected to redirect URI.
     *
     * @param array                                    $scopes can contain \Paysera\WalletApi\OAuth\Consumer::SCOPE_* constants
     * @param string                                   $redirectUri takes current URI without authorization parameters if not passed
     * @param \Paysera\WalletApi\Entity\UserInformation $userInformation if passed, creates OAuth session by API with confirmed user's information
     * @param null                                     $lang
     *
     * @return string
     *
     * @throws OAuthException
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getAuthorizationUri(
        array $scopes = [],
        $redirectUri = null,
        \Paysera\WalletApi\Entity\UserInformation $userInformation = null,
        $lang = null
    ) {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        if ($userInformation === null) {
            $query = http_build_query($this->getOAuthParameters($scopes, $redirectUri), null, '&');

            return $this->router->getOAuthEndpoint($lang) . '?' . $query;
        }
        $parameters = $this->getOAuthParameters($scopes, $redirectUri);
        $responseData = $this->oauthClient->createSession($parameters, $userInformation);

        return $this->router->getOAuthEndpoint($lang) . '/' . $responseData['key'];
    }

    /**
     * @param string      $transactionKey
     * @param string|null $redirectUri
     *
     * @return string
     */
    public function getAuthorizationWithTransactionConfirmationUri(
        $transactionKey,
        $redirectUri = null,
        array $scopes = []
    ) {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        $query = http_build_query(
            $this->getOAuthParameters($scopes, $redirectUri),
            '',
            '&',
        );

        return $this->router->getAuthEndpoint('/transaction/confirm-with-oauth') . '/' . $transactionKey . '?' . $query;
    }

    /**
     * Gets redirect uri for transfer sign.
     *
     * @param string $transferId
     * @param string $redirectUri
     *
     * @return string
     */
    public function getTransferSignRedirectUri($transferId, $redirectUri = null)
    {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }

        return sprintf(
            '%s/%s?%s',
            $this->router->getAuthEndpoint('/wallet/transfer-sign'),
            urlencode($transferId),
            http_build_query(['redirect_uri' => $redirectUri]),
        );
    }

    /**
     * Gets OAuth access token from query parameters. Redirect URI must be the same as passed when getting the
     * authorization URI, otherwise authorization will fail
     * If no authorization parameters are passed, returns null
     * If authorization error is passed or some data is invalid (like state parameter), exception is thrown
     *
     * @param array  $params takes $_GET if not passed
     * @param string $redirectUri takes current URI without authorization parameters if not passed
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken|null
     *
     * @throws OAuthException
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getOAuthAccessToken($params = null, $redirectUri = null)
    {
        if ($params === null) {
            $params = $_GET;
        }
        $authorizationCode = $this->getOAuthCode($params);
        if ($authorizationCode === null) {
            return null;
        }

        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }

        return $this->oauthClient->exchangeCodeForAccessToken($authorizationCode, $redirectUri);
    }

    public function getOAuthParameters(array $scopes, $redirectUri)
    {
        return ['response_type' => 'code', 'client_id' => $this->clientId, 'scope' => implode(
            ' ',
            $scopes,
        ), 'redirect_uri' => $redirectUri, 'state' => $this->createState()];
    }

    public function getOAuthCode(array $params)
    {
        if (!empty($params['code']) || !empty($params['error'])) {
            $currentState = $this->getState();
            $givenState = !empty($params['state']) ? $params['state'] : '';
            if ($currentState !== $givenState) {
                throw new OAuthException(
                    'Invalid state parameter passed in OAuth authentication',
                    'invalid_state',
                );
            }

            if (!empty($params['error'])) {
                $message = 'Error in authentication: ' . $params['error'];
                $message .= !empty($params['error_description']) ? '. ' . $params['error_description'] : '';

                throw new OAuthException($message, $params['error']);
            }

            return $params['code'];
        }

        return null;
    }

    /**
     * Gets password change/reset URI for specific user
     *
     * @param int    $userId
     * @param string $lang
     * @param string $redirectUri
     *
     * @return string
     *
     * @deprecated
     */
    public function getResetPasswordUri($userId, $lang = null, $redirectUri = null)
    {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        $parameters = ['client_id' => $this->clientId, 'redirect_uri' => $redirectUri];
        $query = http_build_query($parameters, '', '&');

        return $this->router->getRemindPasswordUri($userId, $lang) . '?' . $query;
    }

    /**
     * Gets current URI without authentication parameters. Used for getting most probable redirect URI used in
     * authentication request
     *
     * @return string
     */
    protected function getCurrentUri()
    {
        return $this->requestInfo->getCurrentUri(self::$authenticationParameters);
    }

    protected function getState()
    {
        return $this->statePersister->getParameter('oauth-state', null);
    }

    protected function createState()
    {
        $state = $this->generateRandomString();
        $this->statePersister->saveParameter('oauth-state', $state);

        return $state;
    }

    protected function generateRandomString()
    {
        $length = 16;
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[random_int(0, $count - 1)];
        }

        return $str;
    }
}
