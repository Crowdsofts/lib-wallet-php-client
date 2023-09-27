<?php

namespace Paysera\WalletApi\Auth;

use Paysera\WalletApi\Http\Request;

interface SignerInterface
{
    /**
     * Signs request - adds needed headers, changes content or modifies the request in some other way
     *
     * @param array<string, mixed>                          $parameters
     */
    public function signRequest(Request $request, array $parameters = []): void;
}
