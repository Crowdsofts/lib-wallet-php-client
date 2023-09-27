<?php

namespace Paysera\WalletApi\Listener;

/**
 * AccessTokenSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class AccessTokenSetter extends BaseRefreshedTokenListener
{
    /**
     * @param \\Paysera\WalletApi\Client\TokenRelatedWalletClient $tokenRelatedClient
     */
    public function __construct(protected $tokenRelatedClient)
    {
    }

    /**
     */
    public function onTokenRefresh(\Paysera\WalletApi\Event\MacAccessTokenEvent $event)
    {
        $this->tokenRelatedClient->setCurrentAccessToken($event->getToken());
    }
}
