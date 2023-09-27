<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Exception\ApiException;
use Paysera\WalletApi\Exception\ResponseException;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Http\Response;
use Paysera\WalletApi\Mapper;

/**
 * BaseClient. Used as a base for specific clients
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
abstract class BaseClient implements BasicClientInterface
{
    /**
     * Constructs object
     */
    public function __construct(protected BasicClient $client, protected Mapper $mapper)
    {
    }

    /**
     * Makes specified request.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     * @param array                          $options
     *
     * @return mixed|null
     *
     * @throws ApiException
     */
    public function makeRequest(Request $request, $options = []): mixed
    {
        return $this->client->makeRequest($request, $options);
    }

    /**
     * Makes specified request.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     * @param array $options
     *
     * @throws ResponseException
     */
    public function makePlainRequest(Request $request, $options = []): Response
    {
        return $this->client->makePlainRequest($request, $options);
    }

    /**
     * Makes GET request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function get($uri, $options = [])
    {
        return $this->client->get($uri, $options);
    }

    /**
     * Makes DELETE request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function delete($uri, $options = [])
    {
        return $this->client->delete($uri, $options);
    }

    /**
     * Makes POST request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function post($uri, $content = null, $options = [])
    {
        return $this->client->post($uri, $content, $options);
    }

    /**
     * Makes PUT request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function put($uri, $content = null, $options = [])
    {
        return $this->client->put($uri, $content, $options);
    }
}
