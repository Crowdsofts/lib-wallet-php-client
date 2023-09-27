<?php

namespace App\Test\Paysera\WalletApi\Listener;

use Paysera\WalletApi\Client\BasicClient;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Exception\ResponseException;
use Paysera\WalletApi\Http\ClientInterface;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Http\Response;
use Paysera\WalletApi\Listener\InvalidResponseListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InvalidResponseListenerTest extends TestCase
{
    /**
     * @var MockObject|ClientInterface
     */
    protected $webClient;

    /**
     * @var BasicClient
     */
    protected $service;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->webClient = $this->createMock(ClientInterface::class);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new InvalidResponseListener());

        $this->service = new BasicClient(
            $this->webClient,
            $dispatcher,
        );
    }

    #[DataProvider('dataProviderForTestInvalidResponseFallback')]
    public function testSingleInvalidResponseFallback(string $uri, string $json): void
    {
        $this->webClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(new Request($uri))
            ->willReturn(
                new Response(
                    200,
                    [],
                    $json,
                ),
            );

        $this->assertSame(
            $this->service->makeRequest(new Request($uri)),
            $json,
        );
    }


    public function testDoubleInvalidResponse(): void
    {
        $this->expectException(ResponseException::class);

        $this->webClient
            ->expects($this->exactly(2))
            ->method('makeRequest')
            ->with(new Request(''))
            ->willReturn(
                new Response(
                    502,
                    [],
                    '<html>
                    <head><title>502 Bad Gateway</title></head>
                    <body bgcolor="white">
                    <center><h1>502 Bad Gateway</h1></center>
                    <hr><center>nginx/1.0.15</center>
                    </body>
                </html>
                ',
                ),
            );

        $this->service->makeRequest(new Request(''));
    }

    public static function dataProviderForTestInvalidResponseFallback(): array
    {
        $data = [
            'id' => 123,
            'email' => 'user@domain.com',
            'display_name' => 'Username',
        ];

        return [
            ['user/me', json_encode($data, JSON_THROW_ON_ERROR)],
        ];
    }
}
