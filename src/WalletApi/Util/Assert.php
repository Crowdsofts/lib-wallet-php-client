<?php

namespace Paysera\WalletApi\Util;

use Paysera\WalletApi\Exception\LogicException;

class Assert
{
    public static function isInt($value)
    {
        if ((string)(int)$value !== (string)$value) {
            throw new LogicException('Value must be integer');
        }
    }

    public static function isId($value)
    {
        if ((string)$value !== 'me' && (string)(int)$value !== (string)$value) {
            throw new LogicException('Value must be integer or "me"');
        }
    }

    public static function isIntOrNull($value)
    {
        if ($value !== null && (string)(int)$value !== (string)$value) {
            throw new LogicException('Value must be integer');
        }
    }

    public static function isScalar($value)
    {
        if (!is_scalar($value)) {
            throw new LogicException('Value must be string');
        }
    }
}
