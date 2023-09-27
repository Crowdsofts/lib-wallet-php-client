<?php

namespace Paysera\WalletApi\EventDispatcher;

class EventDispatcher
{
    /**
     */
    protected array $listeners = [];

    /**
     */
    protected array $sorted = [];

    /**
     * @var EventDispatcher[]
     */
    protected array $relatedDispatchers = [];

    /**
     * Constructs object
     *
     * @param array $listeners keys are event descriptors, values are callables
     */
    public function __construct(array $listeners = [])
    {
        foreach ($listeners as $eventKey => $listener) {
            $this->addListener($eventKey, $listener);
        }
    }

    /**
     * Dispatches event to all listeners
     *
     * @param string                                  $eventName
     *
     * @return boolean whether at least one listener was registered
     */
    public function dispatch($eventName, Event $event = null): bool|Event|null
    {
        if (null === $event) {
            $event = new Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        if (!isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }

        foreach ($this->sorted[$eventName] as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Adds listener to some event. Does not replace existing - just adds new one to the end of queue
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @throws \Paysera\WalletApi\Exception\ConfigurationException
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        if (!is_callable($listener)) {
            throw new \Paysera\WalletApi\Exception\ConfigurationException('Listener must be a callable');
        }
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);

        foreach ($this->relatedDispatchers as $dispatcher) {
            $dispatcher->addListener($eventName, $listener, $priority);
        }
    }

    public function mergeDispatcher(\Paysera\WalletApi\EventDispatcher\EventDispatcher $dispatcher)
    {
        $dispatcher->relatedDispatchers[] = $this;
        foreach ($dispatcher->listeners as $eventName => $priorities) {
            foreach ($priorities as $priority => $listeners) {
                foreach ($listeners as $listener) {
                    $this->addListener($eventName, $listener, $priority);
                }
            }
        }
    }

    /**
     * @see EventDispatcherInterface::addSubscriber
     *
     * @api
     */
    public function addSubscriber(\Paysera\WalletApi\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, [$subscriber, $params[0]], $params[1] ?? 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
                }
            }
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = [];

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
}
