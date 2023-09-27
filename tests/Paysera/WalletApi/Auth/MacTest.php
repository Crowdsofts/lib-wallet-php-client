<?php

namespace App\Test\Paysera\WalletApi\Auth;

use Paysera\WalletApi\Auth\Mac;
use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Util\NonceGenerator;
use Paysera\WalletApi\Util\Timer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MacTest extends TestCase
{
    #[DataProvider('authProvider')]
    public function testSignRequest(string $uri, string $method, ?string $content, string $mac, string $ext)
    {
        $timerMock = $this->createMock(Timer::class);
        $timerMock->method('getTime')->willReturn(1343818800);
        $nonceGeneratorMock = $this->createMock(NonceGenerator::class);
        $nonceGeneratorMock->method('generate')->willReturn('nQnNaSNyubfPErjRO55yaaEYo9YZfKHN');

        $request = new Request(
            'https://wallet.paysera.com' . $uri,
            $method,
            $content ?? '',
        );
        (new Mac(
            macId: 'wkVd93h2uS',
            macSecret: 'IrdTc8uQodU7PRpLzzLTW6wqZAO6tAMU',
            timer: $timerMock,
            nonceGenerator: $nonceGeneratorMock,
        ))->signRequest($request);

        $authHeader = $request->getHeaderBag()->getHeader('Authorization');

        $this->assertSame(
            'MAC id="wkVd93h2uS", ts="1343818800", nonce="nQnNaSNyubfPErjRO55yaaEYo9YZfKHN", mac="'
            . $mac . '"' . ($ext === '' ? '' : ', ext="' . $ext . '"'),
            $authHeader,
        );
    }

    public static function authProvider(): array
    {
        return [
            [
                '/wallet/rest/v1/payment/10145',
                'GET',
                null,
                'd1OlqI77u2P1IVYIv2ppL3hnrBhyaQ+gDqYMxCH+0e0=',
                '',
            ],
            [
                '/wallet/oauth/v1/',
                'POST',
                'grant_type=authorization_code&code=SplxlOBeZQQYbYS6WxSbIA&redirect_uri=http%3A%2F%2Flocalhost%2Fabc',
                'YEIWoGDeREjJNh+IplpTcCLRNASTAMgFqa540igFcaY=',
                'body_hash=IftzxAtYliLQx46c2JAPidlHKqck0OXD7KmsHNnSptU%3D',
            ],
        ];
    }
}
