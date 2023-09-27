<?php

namespace Paysera\WalletApi\State;

class SessionStatePersister implements StatePersisterInterface
{
    public function __construct(protected string $prefix)
    {
    }

    /**
     * Saves parameter
     *
     */
    public function saveParameter(string $name, mixed $value): void
    {
        $_SESSION[$this->prefix][$name] = $value;
    }

    /**
     * Gets saved parameter
     *
     * @param string $name
     *
     */
    public function getParameter($name, $default = null): mixed
    {
        return $_SESSION[$this->prefix][$name] ?? $default;
    }
}
