<?php

namespace App\Test\Paysera\WalletApi\Entity;

use Paysera\WalletApi\Entity\Client\Host;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HostTest extends TestCase
{
    #[DataProvider('buildRegexpDataProvider')]
    public function testBuildRegexp(int $expected, string $uri, ?string $hostname, ?string $path, ?int $port, string $protocol, bool $anyPort, bool $anySubdomain): void
    {
        $host = new Host();
        $host->setHost($hostname);
        $host->setPath($path);
        $host->setPort($port);
        $host->setProtocol($protocol);
        if ($anyPort) {
            $host->markAsAnyPort();
        }
        if ($anySubdomain) {
            $host->markAsAnySubdomain();
        }
        $this->assertSame($expected, preg_match($host->buildRegexp(), $uri));
    }

    public static function buildRegexpDataProvider(): array
    {
        return [
            [
                1,
                'https://www.example.com/path/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true,
            ],
            [
                0,
                'https://www.example.com/path-other/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true,
            ],
            [
                1,
                'https://www.example.com/path',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true,
            ],
            [
                1,
                'mobile.protocol://',
                null,
                null,
                null,
                'mobile.protocol',
                true,
                false,
            ],
            [
                1,
                'mobile.protocol://path/abc',
                null,
                '/path',
                null,
                'mobile.protocol',
                true,
                false,
            ],
            [
                0,
                'mobile.protocol://other-path',
                null,
                '/path',
                null,
                'mobile.protocol',
                true,
                false,
            ],
            [
                0,
                'https://www.example.com/path-other/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true,
            ],
            [
                1,
                'https://www.example.com/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false,
            ],
            [
                0,
                'https://www.example.com:1010/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false,
            ],
            [
                0,
                'https://a.www.example.com/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false,
            ],
            [
                1,
                'mobile.protocol://?someparam=1&status=error',
                '',
                null,
                null,
                'mobile.protocol',
                true,
                true,
            ],
        ];
    }
}
