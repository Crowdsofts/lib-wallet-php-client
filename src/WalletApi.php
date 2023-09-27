<?php

namespace Paysera;

use Paysera\WalletApi\Auth\SignerInterface;
use Paysera\WalletApi\Callback\Handler;
use Paysera\WalletApi\Client\BasicClient;
use Paysera\WalletApi\Client\OAuthClient;
use Paysera\WalletApi\Client\TokenRelatedWalletClient;
use Paysera\WalletApi\Client\WalletClient;
use Paysera\WalletApi\Container;
use Paysera\WalletApi\Entity\MacAccessToken;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Http\ClientCertificate;
use Paysera\WalletApi\Listener\AccessTokenSetter;
use Paysera\WalletApi\OAuth\Consumer;
use Paysera\WalletApi\Util\Router;

/**
 * WalletApi
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class WalletApi
{
    protected SignerInterface $signer;

    protected Router $router;

    protected Container $container;

    /**
     * Constructor for entry point to client library.
     *
     * Credentials are required.
     * Router can be configured and passed if some other endpoints are used, different from default ones.
     * Container can be passed if some services are overriden with custom ones or if some listeners are bound to
     * event dispatcher.
     *
     * @param string                   $clientId client ID
     * @param string|ClientCertificate $authentication mac secret or certificate information
     * @param Router|null              $router default is used if not passed
     * @param Container|null           $container default is used if not passed
     */
    public function __construct(
        protected string $clientId,
        string|ClientCertificate $authentication,
        ?Router $router = null,
        ?Container $container = null
    ) {
        $this->router = $router ?? new Router();
        $this->container = $container ?? new Container();
        $this->signer = $this->container->createAuthSigner($clientId, $authentication);
    }

    /**
     * Creates wallet client, responsible for creating transactions, getting user information etc.
     *
     * @param array<string, mixed> $parameters project_id, location_id or some other parameters, if needed
     *
     */
    public function walletClient(array $parameters = []): WalletClient
    {
        return $this->getContainer()->createWalletClient(
            $this->basicClient($this->getRouter()->getWalletApiEndpoint(), null, $parameters),
        );
    }

    /**
     * Creates wallet client, related to specific access token. Requests are signed with given access token.
     * Some specific methods are also available, which are only available when using access token.
     *
     * @param array<string, mixed> $parameters project_id, location_id or some other parameters
     */
    public function walletClientWithToken(
        MacAccessToken $token,
        array $parameters = []
    ): TokenRelatedWalletClient {
        $dispatcher = $this->dispatcher($this->getRouter()->getWalletApiEndpoint(), $token, $parameters);
        $client = $this->getContainer()->createWalletClientWithToken(
            $this->getContainer()->createBasicClient($dispatcher),
        );
        $client->setCurrentAccessToken($token);
        $dispatcher->addSubscriber(new AccessTokenSetter($client));

        return $client;
    }

    /**
     * Creates OAuth consumer, responsible for exchanging code to access token, getting code from parameters, getting
     * redirect uri to OAuth endpoint etc. Basically used with "code" grant type
     *
     */
    public function oauthConsumer(): Consumer
    {
        return $this->getContainer()->createOAuthConsumer(
            $this->clientId,
            $this->oauthClient(),
            $this->getRouter(),
        );
    }

    /**
     * Creates callback handler, used when handling callbacks from Wallet API server
     *
     *
     */
    public function callbackHandler(EventDispatcher $dispatcher): Handler
    {
        return $this->getContainer()->createCallbackHandler($dispatcher, $this->getRouter()->getPublicKeyUri());
    }

    /**
     * Creates OAuth client, responsible for getting access token.
     * If "code" grant type is used, usually OAuth consumer is enough for all purposes, client itself is not needed
     *
     */
    public function oauthClient(): OAuthClient
    {
        return $this->getContainer()->createOAuthClient(
            $this->basicClient($this->getRouter()->getOAuthApiEndpoint()),
        );
    }

    /**
     * Creates basic client for making custom requests.
     * Returned client is responsible for signing the requests etc., but no specific methods are defined
     * and no mapping done.
     *
     * This could be used for functionality that is not yet implemented in specific client classes.
     *
     * @param array<string, mixed> $parameters project_id, location_id or some other parameters
     *
     */
    public function basicClient(
        ?string $basePath = null,
        ?MacAccessToken $token = null,
        array $parameters = []
    ): BasicClient {
        return $this->getContainer()->createBasicClient($this->dispatcher($basePath, $token, $parameters));
    }

    /**
     * Returns router, related to API. Can be used to get transaction confirmation URI
     *
     */
    public function router(): ?Router
    {
        return $this->getRouter();
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function dispatcher(
        ?string $basePath = null,
        MacAccessToken $token = null,
        array $parameters = []
    ): EventDispatcher {
        $basePath = $this->getRouter()->getApiEndpoint($basePath);
        if ($token === null) {
            $requestSigner = $this->getContainer()->createRequestSigner($this->signer);
        } else {
            $requestSigner = $this->getContainer()->createOAuthRequestSigner($this->oauthClient(), $token);
        }

        return $this->getContainer()->createDispatcherForClient(
            $basePath,
            $requestSigner,
            $parameters,
        );
    }

    /**
     */
    private function getRouter(): Router
    {
        return $this->router;
    }

    /**
     */
    private function getContainer(): Container
    {
        return $this->container;
    }
}
