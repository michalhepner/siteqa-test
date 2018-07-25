<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Event;

interface EventDispatcherAwareInterface
{
    public function getEventDispatcher(): ?EventDispatcherInterface;
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
