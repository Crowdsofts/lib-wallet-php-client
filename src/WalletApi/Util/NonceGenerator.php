<?php

declare(strict_types=1);

namespace Paysera\WalletApi\Util;

class NonceGenerator
{
    public function generate(int $length = 32): string
    {
        $nonce = '';
        for ($i = 0; $i < $length; $i++) {
            $rnd = random_int(0, 92);
            if ($rnd >= 2) {
                $rnd++;
            }
            if ($rnd >= 60) {
                $rnd++;
            }
            $nonce .= chr(32 + $rnd);
        }

        return $nonce;
    }
}
