<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Event;

interface EventSubscriberInterface
{
    public static function getSubscribedEvents(): array;
}
