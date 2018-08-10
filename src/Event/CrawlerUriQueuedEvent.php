<?php

declare(strict_types = 1);

namespace Siteqa\Test\Event;

use Siteqa\Test\Domain\Model\Uri;

class CrawlerUriQueuedEvent implements EventInterface
{
    const NAME = 'crawler.uri_queued';

    /**
     * @var Uri
     */
    protected $uri;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }
}
