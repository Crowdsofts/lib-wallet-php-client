<?php

namespace Paysera\WalletApi\Entity\Client;

/**
 * Host
 *
 * @author Vytautas Gimbutas <vytautas@gimbutas.net>
 */
class Host
{
    /**
     * @var ?string
     */
    protected ?string $host;

    /**
     * @var int|null
     */
    protected ?int $port;

    /**
     * @var ?string
     */
    protected ?string $path;

    /**
     * @var string|null
     */
    protected ?string $protocol;

    /**
     * @var bool
     */
    protected bool $anyPort = false;

    /**
     * @var bool
     */
    protected bool $anySubdomain = false;

    /**
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public static function create(): Host
    {
        return new self();
    }

    /**
     * Set host
     *
     * @param string $host
     *
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function setHost(?string $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param int $port
     *
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function setPort(?int $port): static
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return int
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set protocol
     *
     * @param string|null $protocol
     *
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function setProtocol(?string $protocol): static
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return null|string
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function markAsAnyPort(): static
    {
        $this->anyPort = true;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function unmarkAsAnyPort(): static
    {
        $this->anyPort = false;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function markAsAnySubdomain(): static
    {
        $this->anySubdomain = true;

        return $this;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Client\Host
     */
    public function unmarkAsAnySubdomain(): static
    {
        $this->anySubdomain = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAnyPort(): bool
    {
        return $this->anyPort;
    }

    /**
     * @return bool
     */
    public function isAnySubdomain(): bool
    {
        return $this->anySubdomain;
    }

    /**
     * Builds regexp from self
     *
     * @return string
     */
    public function buildRegexp(): string
    {
        $regexpParts = [];

        if ($this->getProtocol() !== null) {
            $regexpParts[] = preg_quote($this->getProtocol(), '#') . '\://';
        } else {
            $regexpParts[] = 'https?://';
        }

        $hostname = rtrim((string)$this->getHost(), '/');
        if ($hostname) {
            $hostnameRegexp = preg_quote($hostname, '#');
            if ($this->isAnyPort()) {
                $hostnameRegexp .= '(\:\d+)?';
            } elseif ($this->getPort() !== null) {
                $hostnameRegexp .= ':' . $this->getPort();
            }

            if ($this->isAnySubdomain()) {
                $hostnameRegexp = '([a-zA-Z0-9\-]+\.)*' . $hostnameRegexp;
            }

            $hostnameRegexp .= '/';
            $regexpParts[] = $hostnameRegexp;
        }

        if ($this->getPath()) {
            $regexpParts[] = preg_quote(ltrim($this->getPath(), '/'), '#') . '([/?\\#]|$)';
        }

        return '#^' . implode('', $regexpParts) . '#';
    }
}
