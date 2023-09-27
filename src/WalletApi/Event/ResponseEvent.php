<?php

namespace Paysera\WalletApi\Event;

/**
 * ResponseEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class ResponseEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Http\Response
     */
    protected $response;

    /**
     * @param array                           $options
     */
    public function __construct(\Paysera\WalletApi\Http\Response $response, protected $options)
    {
        $this->response = $response;
    }

    /**
     * Gets response
     *
     * @return \Paysera\WalletApi\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
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

    /**
     * Gets options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
