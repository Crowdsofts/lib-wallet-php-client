<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Event\HttpExceptionEvent;
use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\Event\ResponseEvent;
use Paysera\WalletApi\Event\ResponseExceptionEvent;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Events;
use Paysera\WalletApi\Exception\ApiException;
use Paysera\WalletApi\Exception\HttpException;
use Paysera\WalletApi\Exception\ResponseException;
use Paysera\WalletApi\Http\ClientInterface;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Http\Response;

class BasicClient implements BasicClientInterface
{
    /**
     * Constructs object
     */
    public function __construct(
        protected ClientInterface $webClient,
        protected EventDispatcher $eventDispatcher
    ) {
    }

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
    public function makeRequest(
        Request $request,
        array $options = []
    ) {
        $originalRequest = clone $request;
        $response = $this->makePlainRequestWithReference($request, $options);

        try {
            $responseContent = $response->getContent();

            $contentType = $response->getHeader('content-type');
            if ($contentType === 'application/json') {
                $result = $responseContent !== '' ? json_decode(
                    $responseContent,
                    true,
                    512,
                    JSON_THROW_ON_ERROR,
                ) : null;
            } else {
                $result = $responseContent;
            }

            if (
                ($response->getStatusCode() === 200 && $responseContent === '')
                || ($result === null && $responseContent !== '' && $responseContent !== 'null')
            ) {
                throw new ResponseException(
                    ['error' => 'internal_server_error', 'error_description' => sprintf(
                        'Bad response from server! Response: %s',
                        $responseContent,
                    )],
                    $response->getStatusCode(),
                    $response->getStatusCodeMessage(),
                );
            }

            if ($response->isSuccessful()) {
                return $result;
            }

            throw new ResponseException(
                is_array($result) ? $result : [],
                $response->getStatusCode(),
                $response->getStatusCodeMessage(),
            );
        } catch (ResponseException $exception) {
            $event = new ResponseExceptionEvent($exception, $response, $options);
            $this->eventDispatcher->dispatch(Events::ON_RESPONSE_EXCEPTION, $event);
            if ($event->getResult() !== null) {
                return $event->getResult();
            }

            if ($event->isRepeatRequest()) {
                return $this->makeRequest($originalRequest, $event->getOptions());
            }

            throw $event->getException();
        }
    }

    /**
     * Makes specified request.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     *
     * @return \Paysera\WalletApi\Http\Response
     *
     * @throws ResponseException
     */
    public function makePlainRequest(
        Request $request,
        array $options = []
    ) {
        return $this->makePlainRequestWithReference($request, $options);
    }

    /**
     * Makes specified request with options reference.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     *
     * @throws ResponseException
     */
    private function makePlainRequestWithReference(
        Request $request,
        array &$options = []
    ): Response {
        $event = new RequestEvent($request, $options);
        $this->eventDispatcher->dispatch(Events::BEFORE_REQUEST, $event);
        $options = $event->getOptions();

        try {
            $response = $this->webClient->makeRequest($request);
        } catch (HttpException $exception) {
            $event = new HttpExceptionEvent($exception, $request, $options);
            $this->eventDispatcher->dispatch(Events::ON_HTTP_EXCEPTION, $event);
            if ($event->getResponse() !== null) {
                $response = $event->getResponse();
            } else {
                throw $event->getException();
            }
        }
        $response->setRequest($request);

        $event = new ResponseEvent($response, $options);
        $this->eventDispatcher->dispatch(Events::AFTER_RESPONSE, $event);

        return $response;
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
        return $this->makeRequest(
            new Request(
                $uri,
                Request::METHOD_GET,
            ),
            $options,
        );
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
        return $this->makeRequest(
            new Request(
                $uri,
                Request::METHOD_DELETE,
            ),
            $options,
        );
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
    public function post(
        $uri,
        $content = null,
        $options = []
    ) {
        return $this->makeRequest(
            new Request(
                $uri,
                Request::METHOD_POST,
                $content === null ? '' : json_encode($content, JSON_THROW_ON_ERROR),
                ['Content-Type' => Request::CONTENT_TYPE_JSON],
            ),
            $options,
        );
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
    public function put(
        $uri,
        $content = null,
        $options = []
    ) {
        return $this->makeRequest(
            new Request(
                $uri,
                Request::METHOD_PUT,
                $content === null ? '' : json_encode($content, JSON_THROW_ON_ERROR),
                ['Content-Type' => Request::CONTENT_TYPE_JSON],
            ),
            $options,
        );
    }
}
