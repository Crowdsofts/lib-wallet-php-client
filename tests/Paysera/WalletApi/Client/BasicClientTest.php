<?php

namespace App\Test\Paysera\WalletApi\Client;

use JetBrains\PhpStorm\ExpectedValues;
use Paysera\WalletApi\Client\BasicClient;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Exception\ApiException;
use Paysera\WalletApi\Http\ClientInterface;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BasicClientTest extends TestCase
{
    #[DataProvider('makeRequest_correctly_handles_makeRequest_return_value_provider')]
    public function test_makeRequest_correctly_handles_webClient_makeRequest_corrupt_response(
        int $status,
        ?string $content
    ) {
        $this->expectException(ApiException::class);
        $webClient = $this->createMock(ClientInterface::class);
        $webClient->method('makeRequest')->willReturn(new Response($status, [], (string)$content));
        $basicClient = new BasicClient(
            $webClient,
            $this->createMock(EventDispatcher::class),
        );

        $basicClient->makeRequest(
            new Request(
                'http://example.com/',
                Request::METHOD_GET,
            ),
        );
    }

    public static function makeRequest_correctly_handles_makeRequest_return_value_provider(): array
    {
        return [
            [401, ''],
            [401, null],
            [401, 'str'],

            [200, ''],
            [200, null],
        ];
    }
}
