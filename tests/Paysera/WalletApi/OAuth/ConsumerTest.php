<?php

namespace App\Test\Paysera\WalletApi\OAuth;

use Paysera\WalletApi\Client\OAuthClient;
use Paysera\WalletApi\OAuth\Consumer;
use Paysera\WalletApi\State\SessionStatePersister;
use Paysera\WalletApi\Util\RequestInfo;
use Paysera\WalletApi\Util\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{
    private Consumer $consumer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consumer = new Consumer(
            123,
            $this->createMock(OAuthClient::class),
            new Router(),
            new SessionStatePersister('abc'),
            $this->createMock(RequestInfo::class),
        );
    }

    #[DataProvider('getAuthorizationWithTransactionConfirmationUriDataProvider')]
    public function testGetAuthorizationWithTransactionConfirmationUri(
        string $transactionKey,
        string $redirectUri,
        array $scopes,
        array $expected
    ): void {
        $parsedUrl = parse_url(
            $this->consumer->getAuthorizationWithTransactionConfirmationUri($transactionKey, $redirectUri, $scopes),
        );
        $queryParams = [];
        parse_str($parsedUrl['query'], $queryParams);

        $this->assertEquals($expected['response_type'], $queryParams['response_type']);
        $this->assertEquals($expected['client_id'], $queryParams['client_id']);
        $this->assertEquals($expected['scope'], $queryParams['scope']);
        $this->assertArrayHasKey('state', $queryParams);

        if ($redirectUri !== null) {
            $this->assertEquals($redirectUri, $queryParams['redirect_uri']);
        }

        $this->assertEquals($parsedUrl['scheme'], 'https');
        $this->assertEquals($parsedUrl['host'], 'bank.paysera.com');
        $this->assertEquals($parsedUrl['path'], '/frontend/transaction/confirm-with-oauth/' . $transactionKey);
    }

    public static function getAuthorizationWithTransactionConfirmationUriDataProvider(): array
    {
        return [
            [
                'abc123',
                'https://bank.paysera.com',
                ['scope'],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope',
                    'redirect_uri' => 'https://bank.paysera.com',
                ],
            ],
            [
                'abc123',
                'https://bank.paysera.com',
                ['scope_1', 'scope_2'],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope_1 scope_2',
                ],
            ],
            [
                'abc123',
                'https://bank.paysera.com',
                [],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => '',
                ],
            ],
        ];
    }
}
