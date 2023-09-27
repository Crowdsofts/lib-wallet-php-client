<?php

namespace Paysera\WalletApi\Event;

/**
 * MacAccessTokenEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class MacAccessTokenEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Entity\MacAccessToken
     */
    protected $token;

    public function __construct(\Paysera\WalletApi\Entity\MacAccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * Gets token
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken
     */
    public function getToken()
    {
        return $this->token;
    }
}
