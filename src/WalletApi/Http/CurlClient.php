<?php

namespace Paysera\WalletApi\Http;

/**
 * Web client using cURL library
 */
class CurlClient implements ClientInterface
{
    /**
     * User agent
     *
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (compatible; WalletApi-Curl)';

    /**
     * User header
     *
     * @var array
     */
    protected $userHeader;

    /**
     * Connect timeout in seconds
     *
     * @var int
     */
    protected $connectTimeout = 2;

    /**
     * Request timeout in seconds
     *
     * @var int
     */
    protected $requestTimeout = 60;

    /**
     * User agent setter
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * User header setter
     *
     * @param string $userHeader
     */
    public function setUserHeader($userHeader)
    {
        $this->userHeader = $userHeader;
    }

    /**
     * Connection timeout in seconds setter
     *
     * @param int $connectTimeout
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * Request timeout in seconds setter
     *
     * @param int $requestTimeout
     */
    public function setRequestTimeout($requestTimeout)
    {
        $this->requestTimeout = $requestTimeout;
    }

    /**
     * Makes request to remote server using cURL
     *
     *
     * @return Response
     *
     * @throws \Paysera\WalletApi\Exception\HttpException
     * @throws \Paysera\WalletApi\Exception\ConfigurationException
     */
    public function makeRequest(Request $request): Response
    {
        $curl = $this->getCurl();
        curl_setopt($curl, CURLOPT_URL, $request->getFullUri());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($request->getFormattedHeaders(), ['Expect:']));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getContent());

        $clientCertificate = $request->getClientCertificate();
        if ($clientCertificate !== null) {
            if ($clientCertificate->getCertificatePath() === null || $clientCertificate->getPrivateKeyPath() === null) {
                throw new \Paysera\WalletApi\Exception\ConfigurationException(
                    'If client certificate is provided, both certificate path and private key path must be set',
                );
            }
            curl_setopt($curl, CURLOPT_SSLCERT, $clientCertificate->getCertificatePath());
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, $clientCertificate->getCertificateType());
            curl_setopt($curl, CURLOPT_SSLKEY, $clientCertificate->getPrivateKeyPath());
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, $clientCertificate->getPrivateKeyType());
            if ($clientCertificate->getCertificatePassword() !== null) {
                curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $clientCertificate->getCertificatePassword());
            }
            if ($clientCertificate->getPrivateKeyPassword() !== null) {
                curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $clientCertificate->getPrivateKeyPassword());
            }
        }

        $result = curl_exec($curl);
        if ($result === false) {
            $exception = new \Paysera\WalletApi\Exception\HttpException(curl_error($curl), curl_errno($curl));
            curl_close($curl);

            throw $exception;
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $parts = explode("\r\n\r\n", $result, 2);
        $headers = explode("\r\n", $parts[0]);
        unset($headers[0]);        // remove status code
        $content = $parts[1] ?? '';

        curl_close($curl);

        return new Response($statusCode, $headers, $content);
    }

    /**
     * Gets Curl handle
     *
     * @return resource a cURL handle
     *
     * @throws \Paysera\WalletApi\Exception\ConfigurationException
     */
    protected function getCurl()
    {
        if (!function_exists('curl_init')) {
            throw new \Paysera\WalletApi\Exception\ConfigurationException('Curl is not available on this system');
        }

        $curl = curl_init();
        if (!$curl) {
            throw new \Paysera\WalletApi\Exception\ConfigurationException('Error while initiating curl');
        }

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 0);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->requestTimeout);

        $this->setSslOptions($curl);

        if ($this->userAgent) {
            curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        }

        return $curl;
    }

    /**
     * Sets SSL options to use in cURL. This method MUST NOT be overrided in production environment
     *
     * @param resource $curl
     */
    protected function setSslOptions($curl)
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/../Resources/cacert.pem');
    }
}
