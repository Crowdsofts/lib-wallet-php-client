<?php

namespace Paysera\WalletApi\Event;

use Paysera\WalletApi\EventDispatcher\Event;
use Paysera\WalletApi\Http\Request;

/**
 * RequestEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class RequestEvent extends Event
{
    public function __construct(protected Request $request, protected array $options)
    {
    }

    /**
     * Gets request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets options
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
