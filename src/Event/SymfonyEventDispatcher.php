<?php

declare(strict_types = 1);

namespace Siteqa\Test\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as SymfonyEventSubscriberInterface;

class SymfonyEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $subscriberPairs = [];

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(EventInterface $event): EventInterface
    {
        $dynamicEvent = new class($event) extends SymfonyEvent implements EventInterface {
            /**
             * @var EventInterface
             */
            private $originalEvent;

            public function __construct(EventInterface $originalEvent)
            {
                $this->originalEvent = $originalEvent;
            }

            public function getName(): string
            {
                return $this->originalEvent->getName();
            }

            public function __call($name, $arguments)
            {
                return call_user_func_array([$this->originalEvent, $name], $arguments);
            }
        };

        $this->eventDispatcher->dispatch(call_user_func(get_class($event).'::getName'), $dynamicEvent);

        return $dynamicEvent;
    }

    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $subscriberWrapper = new class($subscriber) implements SymfonyEventSubscriberInterface {
            private static $subscribedEvents;

            /**
             * @var EventSubscriberInterface
             */
            protected $originalSubscriber;

            public function __construct(EventSubscriberInterface $originalSubscriber)
            {
                $this->originalSubscriber = $originalSubscriber;
            }

            public static function getSubscribedEvents()
            {
                return self::$subscribedEvents;
            }

            public function setSubscribedEvents($events)
            {
                self::$subscribedEvents = $events;
            }

            public function __call($name, $arguments)
            {
                return call_user_func_array([$this->originalSubscriber, $name], $arguments);
            }

            public function getOriginalSubscriber(): EventSubscriberInterface
            {
                return $this->originalSubscriber;
            }
        };

        $subscriberWrapper->setSubscribedEvents(call_user_func(get_class($subscriber).'::getSubscribedEvents'));
        $this->subscriberPairs[] = [$subscriber, $subscriberWrapper];
        $this->eventDispatcher->addSubscriber($subscriberWrapper);
    }

    public function removeListener(string $eventName, callable $listener): void
    {
        $this->eventDispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($this->subscriberPairs as $key => $subscriberPair) {
            list($tmpSubscriber, $tmpSubscriberWrapper) = $subscriberPair;

            if ($tmpSubscriber === $subscriber) {
                $this->eventDispatcher->removeSubscriber($tmpSubscriberWrapper);
                unset($this->subscriberPairs[$key]);
            }
        }
    }

    public function getListeners(?string $eventName = null): array
    {
        return $this->eventDispatcher->getListeners($eventName);
    }

    public function getListenerPriority(string $eventName, callable $listener): ?int
    {
        return $this->eventDispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners(?string $eventName = null): bool
    {
        return $this->eventDispatcher->hasListeners($eventName);
    }
}
