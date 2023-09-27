<?php

namespace Paysera\WalletApi;

use Paysera\WalletApi\Auth\Mac;
use Paysera\WalletApi\Auth\SignerInterface;
use Paysera\WalletApi\Callback\Handler;
use Paysera\WalletApi\Callback\SignChecker;
use Paysera\WalletApi\Client\BasicClient;
use Paysera\WalletApi\Client\OAuthClient;
use Paysera\WalletApi\Client\TokenRelatedWalletClient;
use Paysera\WalletApi\Client\WalletClient;
use Paysera\WalletApi\Entity\MacAccessToken;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\EventDispatcher\EventSubscriberInterface;
use Paysera\WalletApi\Http\ClientCertificate;
use Paysera\WalletApi\Http\ClientInterface;
use Paysera\WalletApi\Http\CurlClient;
use Paysera\WalletApi\Listener\AppendHeadersListener;
use Paysera\WalletApi\Listener\EndpointSetter;
use Paysera\WalletApi\Listener\InvalidResponseListener;
use Paysera\WalletApi\Listener\OAuthRequestSigner;
use Paysera\WalletApi\Listener\ParameterSetter;
use Paysera\WalletApi\Listener\RequestSigner;
use Paysera\WalletApi\OAuth\Consumer;
use Paysera\WalletApi\State\SessionStatePersister;
use Paysera\WalletApi\State\StatePersisterInterface;
use Paysera\WalletApi\Util\RequestInfo;
use Paysera\WalletApi\Util\Router;

/**
 * Service and parameter container class.
 * Only creates custom services, used in this API. Contains default creation login inside.
 * Creates services only when needed, not on initialization
 */
class Container
{
    protected ?Mapper $mapper = null;

    protected ?ClientInterface $webClient = null;

    protected ?EventDispatcher $eventDispatcher = null;

    protected ?StatePersisterInterface $statePersister = null;

    /**
     * For customizing service. In normal cases this should not be called
     *
     *
     * @return self for fluent interface
     */
    public function setMapper(Mapper $mapper): static
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     *
     * @return self for fluent interface
     */
    public function setWebClient(ClientInterface $webClient): static
    {
        $this->webClient = $webClient;

        return $this;
    }

    /**
     * Sets eventDispatcher
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher($eventDispatcher): static
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Sets statePersister
     *
     * @param StatePersisterInterface $statePersister
     *
     * @return $this
     */
    public function setStatePersister($statePersister): static
    {
        $this->statePersister = $statePersister;

        return $this;
    }

    /**
     *
     */
    public function createDispatcherForClient(
        string $basePath,
        EventSubscriberInterface $requestSigner,
        array $parameters = []
    ): EventDispatcher {
        $dispatcher = new EventDispatcher();
        $dispatcher->mergeDispatcher($this->getEventDispatcher());
        $dispatcher->addSubscriber(new EndpointSetter($basePath));

        if (count($parameters) > 0) {
            $dispatcher->addSubscriber(new ParameterSetter($parameters));

            if (isset($parameters['headers'])) {
                $dispatcher->addSubscriber(
                    new AppendHeadersListener($parameters['headers']),
                );
            }
        }

        $dispatcher->addSubscriber($requestSigner);
        $dispatcher->addSubscriber($this->createInvalidResponseListener());

        return $dispatcher;
    }

    /**
     */
    public function createBasicClient(EventDispatcher $dispatcher): BasicClient
    {
        return new BasicClient($this->getWebClient(), $dispatcher);
    }

    /**
     */
    public function createOAuthClient(BasicClient $basicClient): OAuthClient
    {
        return new OAuthClient($basicClient, $this->getMapper());
    }

    /**
     */
    public function createWalletClient(BasicClient $basicClient): WalletClient
    {
        return new WalletClient($basicClient, $this->getMapper());
    }

    /**
     */
    public function createWalletClientWithToken(BasicClient $basicClient): TokenRelatedWalletClient
    {
        return new TokenRelatedWalletClient($basicClient, $this->getMapper());
    }

    /**
     */
    public function createRequestSigner(SignerInterface $signer): RequestSigner
    {
        return new RequestSigner($signer);
    }

    /**
     */
    public function createInvalidResponseListener(): InvalidResponseListener
    {
        return new InvalidResponseListener();
    }

    /**
     *
     * @return OAuthRequestSigner
     */
    public function createOAuthRequestSigner(
        OAuthClient $client,
        MacAccessToken $token
    ) {
        return new OAuthRequestSigner($client, $token);
    }

    /**
     * Creates OAuth consumer service
     *
     *
     */
    public function createOAuthConsumer(
        string $clientId,
        OAuthClient $oauthClient,
        Router $router
    ): Consumer {
        return new Consumer(
            $clientId,
            $oauthClient,
            $router,
            $this->getStatePersister('Paysera_WalletApi_' . $clientId),
            new RequestInfo($_SERVER),
        );
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @param string|ClientCertificate $authentication string for MAC secret
     */
    public function createAuthSigner(string $clientId, ClientCertificate|string $authentication): SignerInterface
    {
        if ($authentication instanceof ClientCertificate) {
            return new ClientCertificate($authentication);
        }

        return new Mac($clientId, $authentication);
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     *
     * @return Handler
     */
    public function createCallbackHandler(EventDispatcher $dispatcher, string $publicKeyUri)
    {
        return new Handler(
            $dispatcher,
            new SignChecker($publicKeyUri, $this->getWebClient()),
            $this->getMapper(),
        );
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher ?? new EventDispatcher();
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     */
    public function getMapper(): Mapper
    {
        return $this->mapper ?? new Mapper();
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     */
    protected function getWebClient(): ClientInterface
    {
        return $this->webClient ?? new CurlClient();
    }

    /**
     * Returns configured state persister. If not configured, creates default one with given prefix
     *
     */
    protected function getStatePersister($prefix): StatePersisterInterface
    {
        return $this->statePersister ?? new SessionStatePersister($prefix);
    }
}
