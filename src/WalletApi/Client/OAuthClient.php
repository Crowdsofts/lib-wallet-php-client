<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Entity\UserInformation;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Util\Assert;

/**
 * OAuth Client
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class OAuthClient extends BaseClient
{
    /**
     * Exchanges authorization code for access token. Use this method only if you make custom "code" parameter handling.
     * Use getOAuthAccessToken method instead for usual uses.
     *
     * @param string $authorizationCode
     * @param string $redirectUri
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     *
     * @see \Paysera\WalletApi::getOAuthAccessToken
     */
    public function exchangeCodeForAccessToken($authorizationCode, $redirectUri = null)
    {
        $parameters = ['grant_type' => 'authorization_code', 'code' => $authorizationCode];
        if ($redirectUri !== null) {
            $parameters['redirect_uri'] = $redirectUri;
        }
        $responseData = $this->post('token', $parameters);

        return $this->mapper->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string $username
     * @param string $password
     * @param array  $scopes   can contain \Paysera\WalletApi\OAuth\Consumer::SCOPE_* constants
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function exchangePasswordForAccessToken($username, $password, array $scopes = [])
    {
        Assert::isScalar($username);
        Assert::isScalar($password);
        $parameters = ['grant_type' => 'password', 'username' => $username, 'password' => $password, 'scope' => implode(
            ' ',
            $scopes,
        )];
        $responseData = $this->post('token', $parameters);

        return $this->mapper->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string     $refreshToken
     * @param array|null $scopes       can contain \Paysera\WalletApi\OAuth\Consumer::SCOPE_* constants
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function refreshAccessToken($refreshToken, $scopes = null)
    {
        Assert::isScalar($refreshToken);
        $parameters = ['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken];
        if ($scopes !== null) {
            $parameters['scope'] = implode(' ', $scopes);
        }
        $responseData = $this->post('token', $parameters);

        return $this->mapper->decodeAccessToken($responseData);
    }

    public function revokeAccessToken($token)
    {
        Assert::isScalar($token);
        $this->delete(sprintf('token?access_token=%s', $token));
    }

    /**
     * Creates OAuth session. Used for passing confirmed user information, if available
     *
     *
     * @return mixed|null
     */
    public function createSession(array $parameters, UserInformation $userInformation)
    {
        $parameters['user'] = $this->mapper->encodeUserInformation($userInformation);

        return $this->post('session', $parameters);
    }

    /**
     * Makes POST request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to URL-encoded format
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function post($uri, $content = null, $options = [])
    {
        return $this->makeRequest(new Request(
            $uri,
            Request::METHOD_POST,
            $content === null ? '' : http_build_query($content, null, '&'),
            ['Content-Type' => Request::CONTENT_TYPE_URLENCODED],
        ), $options);
    }

    /**
     * Makes PUT request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to URL-encoded format
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function put($uri, $content = null, $options = [])
    {
        return $this->makeRequest(new Request(
            $uri,
            Request::METHOD_PUT,
            $content === null ? '' : http_build_query($content, null, '&'),
            ['Content-Type' => Request::CONTENT_TYPE_URLENCODED],
        ), $options);
    }
}
