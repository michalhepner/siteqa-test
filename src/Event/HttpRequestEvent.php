<?php

declare(strict_types = 1);

namespace Siteqa\Test\Event;

use Siteqa\Test\Domain\Model\HttpRequest;

class HttpRequestEvent implements EventInterface
{
    const NAME = 'http_request';

    /**
     * @var HttpRequest
     */
    protected $request;

    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }
}
