<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use Siteqa\Test\Domain\Collection\UriCollection;

class SitemapResult
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var UriCollection
     */
    protected $uris;

    /**
     * @var HttpSuite
     */
    protected $httpSuite;

    public function __construct(Sitemap $sitemap, UriCollection $uris, HttpSuite $httpSuite)
    {
        $this->sitemap = $sitemap;
        $this->uris = $uris;
        $this->httpSuite = $httpSuite;
    }

    public function getHttpSuite(): HttpSuite
    {
        return $this->httpSuite;
    }

    public function getUris(): UriCollection
    {
        return $this->uris;
    }

    public function getSitemap(): Sitemap
    {
        return $this->sitemap;
    }
}
