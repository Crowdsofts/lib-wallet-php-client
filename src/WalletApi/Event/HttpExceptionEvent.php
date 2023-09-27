<?php

namespace Paysera\WalletApi\Event;

/**
 * HttpExceptionEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class HttpExceptionEvent extends \Paysera\WalletApi\EventDispatcher\Event
{
    /**
     * @var \Paysera\WalletApi\Exception\ResponseException
     */
    protected $exception;

    /**
     * @var \Paysera\WalletApi\Http\Response
     */
    protected $response;

    /**
     * @var \Paysera\WalletApi\Http\Request
     */
    protected $request;

    /**
     * @param array                                     $options
     */
    public function __construct(
        \Paysera\WalletApi\Exception\HttpException $exception,
        \Paysera\WalletApi\Http\Request $request,
        protected $options
    ) {
        $this->exception = $exception;
        $this->request = $request;
    }

    /**
     * Sets exception
     *
     * @param \Paysera\WalletApi\Exception\ResponseException $exception
     *
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Gets exception
     *
     * @return \Paysera\WalletApi\Exception\ResponseException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets response
     *
     * @param \Paysera\WalletApi\Http\Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
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
     * Gets request
     *
     * @return \Paysera\WalletApi\Http\Request
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
}
