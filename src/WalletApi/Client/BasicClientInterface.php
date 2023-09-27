<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Exception\ApiException;
use Paysera\WalletApi\Http\Request;

interface BasicClientInterface
{
    /**
     * Makes specified request.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     *
     * @return mixed|null
     *
     * @throws ApiException
     */
    public function makeRequest(Request $request, array $options = []);

    /**
     * Makes GET request, uri can be relative to current context (without endpoint and API path)
     *
     *
     * @return mixed|null
     */
    public function get(string $uri, array $options = []);

    /**
     * Makes DELETE request, uri can be relative to current context (without endpoint and API path)
     *
     *
     * @return mixed|null
     */
    public function delete(string $uri, array $options = []);

    /**
     * Makes POST request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     *
     * @return mixed|null
     */
    public function post(string $uri, mixed $content = null, array $options = []);

    /**
     * Makes PUT request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     *
     * @return mixed|null
     */
    public function put(string $uri, mixed $content = null, array $options = []);
}
