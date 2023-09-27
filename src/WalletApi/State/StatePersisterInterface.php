<?php

namespace Paysera\WalletApi\State;

interface StatePersisterInterface
{
    /**
     * Saves parameter
     *
     */
    public function saveParameter(string $name, mixed $value);

    /**
     * Gets saved parameter
     *
     *
     */
    public function getParameter(string $name, mixed $default = null): mixed;
}
