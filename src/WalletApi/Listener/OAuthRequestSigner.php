<?php

namespace Paysera\WalletApi\Listener;

use Paysera\WalletApi\Auth\Mac;
use Paysera\WalletApi\Client\OAuthClient;
use Paysera\WalletApi\Entity\MacAccessToken;
use Paysera\WalletApi\Event\MacAccessTokenEvent;
use Paysera\WalletApi\Event\ResponseExceptionEvent;
use Paysera\WalletApi\Events;

/**
 * OAuthRequestSigner
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class OAuthRequestSigner extends RequestSigner
{
    /**
     * @var MacAccessToken
     */
    protected $token;

    /**
     * @var OAuthClient
     */
    protected $oauthClient;

    /**
     * @param OAuthClient      $oauthClient related with client credentials
     */
    public function __construct(
        OAuthClient $oauthClient,
        MacAccessToken $token
    ) {
        $this->oauthClient = $oauthClient;
        $this->token = $token;
        $this->signer = new Mac($token->getMacId(), $token->getMacKey());
    }

    public function onResponseException(ResponseExceptionEvent $event)
    {
        if ($event->getException()->getErrorCode() === 'invalid_grant') {
            $options = $event->getOptions();
            if (!isset($options['oauth_access_token_retry'])) {
                $options['oauth_access_token_retry'] = true;
                $event->setOptions($options);

                $refreshToken = $this->token->getRefreshToken();
                if ($refreshToken !== null) {
                    $newToken = $this->oauthClient->refreshAccessToken($refreshToken);
                    $this->token = $newToken;
                    $this->signer = new Mac($newToken->getMacId(), $newToken->getMacKey());
                    $event->stopPropagation()->setRepeatRequest(true);

                    $event->getDispatcher()->dispatch(
                        Events::AFTER_OAUTH_TOKEN_REFRESH,
                        new MacAccessTokenEvent($newToken),
                    );
                }
            }
        }
    }

    /**
     * Gets token
     *
     */
    public function getToken(): MacAccessToken
    {
        return $this->token;
    }

    public static function getSubscribedEvents(): array
    {
        return parent::getSubscribedEvents() + [Events::ON_RESPONSE_EXCEPTION => 'onResponseException'];
    }
}
