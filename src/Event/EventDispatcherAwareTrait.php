<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Event;

trait EventDispatcherAwareTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(EventInterface $event): EventInterface
    {
        if ($this->eventDispatcher) {
            return $this->eventDispatcher->dispatch($event);
        }

        return $event;
    }
}
