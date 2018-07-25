<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Domain\Model;

class Sitemap
{
    /**
     * @var Uri
     */
    private $uri;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function setUri(Uri $uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}
