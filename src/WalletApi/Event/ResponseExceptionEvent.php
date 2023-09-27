<?php

namespace Paysera\WalletApi\Event;

/**
 * ResponseExceptionEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class ResponseExceptionEvent extends \Paysera\WalletApi\EventDispatcher\Event
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
     */
    protected $result;

    /**
     * @var bool
     */
    protected $repeatRequest = false;

    /**
     * @param array                                         $options
     */
    public function __construct(
        \Paysera\WalletApi\Exception\ResponseException $exception,
        \Paysera\WalletApi\Http\Response $response,
        protected $options
    ) {
        $this->exception = $exception;
        $this->response = $response;
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
     * Gets response
     *
     * @return \Paysera\WalletApi\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets result
     *
     *
     * @return $this
     */
    public function setResult(mixed $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Gets result
     *
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Sets repeatRequest
     *
     * @param boolean $repeatRequest
     *
     * @return $this
     */
    public function setRepeatRequest($repeatRequest)
    {
        $this->repeatRequest = $repeatRequest;

        return $this;
    }

    /**
     * Gets repeatRequest
     *
     * @return boolean
     */
    public function isRepeatRequest()
    {
        return $this->repeatRequest;
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
