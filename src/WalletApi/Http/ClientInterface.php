<?php

namespace Paysera\WalletApi\Http;

/**
 * Interface for web client
 */
interface ClientInterface
{
    /**
     * Makes request to remote server
     *
     *
     * @return Response
     *
     * @throws \Paysera\WalletApi\Exception\HttpException
     * @throws \Paysera\WalletApi\Exception\ConfigurationException
     */
    public function makeRequest(Request $request): Response;
}
