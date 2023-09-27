<?php

namespace Paysera\WalletApi\Callback;

use Paysera\WalletApi\Exception\CallbackException;
use Paysera\WalletApi\Exception\HttpException;
use Paysera\WalletApi\Http\ClientInterface;
use Paysera\WalletApi\Http\Request;

/**
 * Checks whether callback sign is valid
 */
class SignChecker
{
    /**
     * Constructs object
     */
    public function __construct(protected string $publicKeyUri, protected ClientInterface $webClient)
    {
        $this->webClient = $webClient;
    }

    /**
     * Checks whether callback sign is valid. Before checking, downloads public key from Wallet server
     *
     * @param string $data
     * @param string $sign
     *
     * @return boolean
     *
     * @throws \Paysera\WalletApi\Exception\CallbackException
     */
    public function checkSign($data, $sign)
    {
        return $this->checkSignWithPublicKey($data, $sign, $this->getPublicKey());
    }

    /**
     * Downloads public key
     *
     * @return string
     *
     * @throws \Paysera\WalletApi\Exception\CallbackException
     */
    protected function getPublicKey()
    {
        try {
            return $this->webClient->makeRequest(new Request($this->publicKeyUri))->getContent();
        } catch (HttpException $exception) {
            throw new CallbackException(
                'Cannot get public key from Wallet server',
                0,
                $exception,
            );
        }
    }

    /**
     * Checks whether callback sign is valid, providing public key to use
     *
     *
     * @return boolean
     *
     * @throws CallbackException
     */
    protected function checkSignWithPublicKey(string $data, string $sign, string $publicKey): bool
    {
        while (openssl_error_string()) {
            // empty error buffer
        }
        $result = openssl_verify($data, base64_decode($sign), $publicKey, 'sha256');
        if ($result === -1) {
            throw new CallbackException(
                'OpenSSL error, probably incorrect public key from Wallet system: ' . openssl_error_string(),
            );
        }

        return $result === 1;
    }
}
