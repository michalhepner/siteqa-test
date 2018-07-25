<?php

declare(strict_types = 1);

namespace Siteqa\Test\Event;

interface EventDispatcherAwareInterface
{
    public function getEventDispatcher(): ?EventDispatcherInterface;
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
