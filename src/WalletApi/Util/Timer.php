<?php

declare(strict_types=1);

namespace Paysera\WalletApi\Util;

class Timer
{
    public function getTime(): int
    {
        return time();
    }
}
