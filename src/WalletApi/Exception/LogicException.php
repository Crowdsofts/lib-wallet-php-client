<?php

namespace Paysera\WalletApi\Exception;

/**
 * Thrown when library is used not as it should be. For example, if trying to create payment which already has an ID
 */
class LogicException extends ApiException
{
}
