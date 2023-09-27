<?php

namespace Paysera\WalletApi\Auth;

use Paysera\WalletApi\Http\Request;

/**
 * Signs requests by adding certificate info before sending them to API
 */
class ClientCertificate implements SignerInterface
{
    public const HEADER_PREFIX = 'Wallet-Api-';

    /**
     * Constructs object
     */
    public function __construct(protected \Paysera\WalletApi\Http\ClientCertificate $clientCertificate)
    {
    }

    /**
     * Signs request - adds Authorization header with generated value
     *
     * @param array<string, mixed> $parameters
     */
    public function signRequest(Request $request, array $parameters = []): void
    {
        $request->setClientCertificate(clone $this->clientCertificate);

        foreach ($parameters as $name => $value) {
            $name = implode('-', array_map('ucfirst', explode('-', str_replace('_', '-', $name))));
            $request->setHeader(self::HEADER_PREFIX . $name, $value);
        }
    }
}
