<?php

namespace Paysera\WalletApi\Auth;

use Paysera\WalletApi\Http\Request;
use Paysera\WalletApi\Util\NonceGenerator;
use Paysera\WalletApi\Util\Timer;

/**
 * Signs requests before sending them to API
 */
class Mac implements SignerInterface
{
    public function __construct(
        protected string $macId,
        protected string $macSecret,
        protected ?Timer $timer = null,
        protected ?NonceGenerator $nonceGenerator = null
    ) {
        if (null === $this->timer) {
            $this->timer = new Timer();
        }
        if (null === $this->nonceGenerator) {
            $this->nonceGenerator = new NonceGenerator();
        }
    }

    /**
     * Signs request - adds Authorization header with generated value
     *
     * @param array<string, mixed> $parameters
     */
    public function signRequest(Request $request, array $parameters = []): void
    {
        $timestamp = $this->getTimestamp();
        $nonce = $this->generateNonce();
        $ext = $this->generateExt($request, $parameters);
        $mac = $this->calculateMac(
            (string)$timestamp,
            $nonce,
            $request->getMethod(),
            $request->getUri(),
            $request->getHost(),
            (string)$request->getPort(),
            $ext,
            $this->macSecret,
        );

        $params = ['id' => $this->macId, 'ts' => $timestamp, 'nonce' => $nonce, 'mac' => $mac];
        if ($ext != '') {
            $params['ext'] = $ext;
        }
        $parts = [];
        foreach ($params as $name => $value) {
            $parts[] = $name . '="' . $value . '"';
        }
        $authenticationHeader = 'MAC ' . implode(', ', $parts);

        $request->setHeader('Authorization', $authenticationHeader);
    }

    protected function getTimestamp(): int
    {
        return $this->timer->getTime();
    }

    /**
     * Generates pseudo-random nonce value
     *
     *
     */
    protected function generateNonce(int $length = 32): string
    {
        return $this->nonceGenerator->generate($length);
    }

    /**
     * Generates ext field for this request to be used in MAC authorization header
     *
     *
     * @param array<string, mixed> $parameters
     */
    protected function generateExt(Request $request, array $parameters): string
    {
        $content = $request->getContent();
        $extParts = [];
        if ($content !== '') {
            $extParts['body_hash'] = base64_encode(hash('sha256', $content, true));
        }
        $extParts += $parameters;
        if (count($extParts) > 0) {
            return http_build_query($extParts);
        }

        return '';
    }

    /**
     * Calculates MAC value by provided arguments
     */
    protected function calculateMac(
        string $timestamp,
        string $nonce,
        string $method,
        string $uri,
        string $host,
        string $port,
        string $ext,
        string $secret
    ): string {
        $normalizedRequest = implode("\n", [$timestamp, $nonce, $method, $uri, $host, $port, $ext, '']);

        return base64_encode(hash_hmac('sha256', $normalizedRequest, $secret, true));
    }
}
