<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use DateTime;
use Siteqa\Test\Domain\Collection\HttpHeaderCollection;

class HttpRequest
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var HttpHeaderCollection
     */
    private $headers;

    /**
     * @var DateTime
     */
    private $created;

    public function __construct(Uri $uri, ?HttpHeaderCollection $headers = null, ?DateTime $created = null)
    {
        $this->setUri($uri);
        $this->setHeaders($headers ?? new HttpHeaderCollection());
        $this->setCreated($created ?? new DateTime('now'));
    }

    public function setUri(Uri $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function setHeaders(HttpHeaderCollection $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders(): HttpHeaderCollection
    {
        return $this->headers;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }
}
