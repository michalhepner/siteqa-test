<?php

declare(strict_types = 1);

namespace Siteqa\Test\Provider;

use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\HttpResponse;
use Siteqa\Test\Domain\Model\Sitemap;
use Siteqa\Test\Domain\Model\SitemapResult;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Sitemap\UriExtractor;

class SitemapResultProvider
{
    /**
     * @var HttpSuiteProviderInterface
     */
    protected $httpSuiteProvider;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    public function __construct(HttpSuiteProviderInterface $httpSuiteProvider)
    {
        $this->httpSuiteProvider = $httpSuiteProvider;
    }

    public function provide(Sitemap $sitemap): SitemapResult
    {
        $httpSuite = $this->httpSuiteProvider->provideSync($sitemap->getUri());
        $uris = $this->getUrls($httpSuite->getResponse());

        return new SitemapResult($sitemap, $uris, $httpSuite);
    }

    protected function getUrls(?HttpResponse $response): UriCollection
    {
        return new UriCollection(
            array_map(
                function (string $uri) {
                    return Uri::createFromString($uri);
                },
                $response ? (new UriExtractor())->extract($response->getBody()) : []
            )
        );
    }
}
