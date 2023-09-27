<?php

namespace Paysera\WalletApi\Exception;

/**
 * Thrown on OAuth error
 */
class OAuthException extends ApiException
{
    /**
     * Constructs object
     *
     * @param string     $message
     * @param string     $errorCode
     * @param Exception $exception
     */
    public function __construct($message, protected $errorCode, $exception = null)
    {
        parent::__construct($message, 0, $exception);
    }

    /**
     * Gets error code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
