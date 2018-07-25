<?php

declare(strict_types = 1);

namespace Siteqa\Test\Sitemap;

use Exception;
use Symfony\Component\DomCrawler\Crawler;

class UriExtractor
{
    /**
     * @param string $xml
     * @return string[]
     */
    public function extract(string $xml): array
    {
        $uris = [];
        try {
            (new Crawler($xml))->filter('*')->each(function (Crawler $crawler) use (&$uris) {
                $crawler->nodeName() === 'loc' && $uris[] = $crawler->text();
            });
        } catch (Exception $exception) {
            // In case of empty node list an exception is thrown. We don't want this.
        }

        return $uris;
    }
}
