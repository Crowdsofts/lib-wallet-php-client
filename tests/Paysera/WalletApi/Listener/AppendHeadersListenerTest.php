<?php

namespace App\Test\Paysera\WalletApi\Listener;

use Paysera\WalletApi\Auth\Mac;
use Paysera\WalletApi\Container;
use Paysera\WalletApi\Event\RequestEvent;
use Paysera\WalletApi\EventDispatcher\EventDispatcher;
use Paysera\WalletApi\Events;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Listener\RequestSigner;
use PHPUnit\Framework\TestCase;

class AppendHeadersListenerTest extends TestCase
{
    public function testHeadersIsDefined(): void
    {
        $eventDispatcher = $this->getEventDispatcher(
            [
                'headers' => [
                    'Header-Name' => 'value',
                    'Header-Another-Name' => 'value',
                ],
            ],
        );

        $request = new Request(
            'https://test.dev/rest/v1/wallet/me/balance',
        );

        $event = new RequestEvent($request, []);
        $eventDispatcher->dispatch(Events::BEFORE_REQUEST, $event);

        $this->assertEquals(
            'value',
            $request->getHeaderBag()->getHeader('Header-Name'),
        );

        $this->assertEquals(
            'value',
            $request->getHeaderBag()->getHeader('Header-Another-Name'),
        );
    }

    public function testHeadersIsNotDefined(): void
    {
        $eventDispatcher = $this->getEventDispatcher(['random' => 'rand']);

        $request = new Request(
            'https://test.dev/rest/v1/wallet/me/balance',
        );

        $event = new RequestEvent($request, []);
        $eventDispatcher->dispatch(Events::BEFORE_REQUEST, $event);

        $this->assertNull($request->getHeaderBag()->getHeader('random'));
    }

    private function getEventDispatcher(array $parameters = []): EventDispatcher
    {
        $requestSigner = new RequestSigner(
            new Mac('123', '555'),
        );

        return (new Container())->createDispatcherForClient(
            'https://test.dev/',
            $requestSigner,
            $parameters,
        );
    }
}
