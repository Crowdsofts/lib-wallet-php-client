<?php

namespace Paysera\WalletApi\EventDispatcher;

class Event
{
    /**
     * @var bool Whether no further event listeners should be triggered
     */
    protected bool $propagationStopped = false;

    protected EventDispatcher $dispatcher;

    /**
     * @var string This event's name
     */
    protected string $name;

    /**
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event
     *
     * @return $this
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;

        return $this;
    }

    /**
     * Stores the EventDispatcher that dispatches this Event
     *
     *
     * @api
     */
    public function setDispatcher(EventDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns the EventDispatcher that dispatches this Event
     *
     */
    public function getDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }

    /**
     * Gets the event's name.
     *
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the event's name property.
     *
     * @param string $name The event name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
