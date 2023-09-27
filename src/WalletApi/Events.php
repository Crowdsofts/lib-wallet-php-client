<?php

namespace Paysera\WalletApi;

/**
 * Events
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
final class Events
{
    public const BEFORE_REQUEST = 'paysera.wallet_api.http.before_request';
    public const AFTER_RESPONSE = 'paysera.wallet_api.http.after_request';

    public const ON_HTTP_EXCEPTION = 'paysera.wallet_api.exception.http';
    public const ON_RESPONSE_EXCEPTION = 'paysera.wallet_api.exception.response';

    public const AFTER_OAUTH_TOKEN_REFRESH = 'paysera.wallet_api.oauth.after_token_refresh';
}
