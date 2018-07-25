<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Domain\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Siteqa\Test\Domain\Model\HttpResponse;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Domain\Collection\HttpSuiteCollection;
use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\CrawlerResult;
use Siteqa\Test\Domain\Model\HttpRequest;
use Siteqa\Test\Domain\Model\HttpSuite;

class CrawlerResultTest extends TestCase
{
    public function test(): void
    {
        $crawlerResult = new CrawlerResult(
            $initialUris = new UriCollection([
                $uri1 = Uri::createFromString('https://example.com/page1'),
                $uri2 = Uri::createFromString('https://example.com/page2'),
            ]),
            $allowedHosts = ['example.com'],
            $recursive = true,
            $httpSuites = new HttpSuiteCollection([
                $httpSuite1 = new HttpSuite(
                    $uri1,
                    $this->createMock(HttpRequest::class),
                    $this->createMock(HttpResponse::class),
                    null,
                    null,
                    123,
                    new DateTime('now')
                ),
                $httpSuite2 = new HttpSuite(
                    $uri2,
                    $this->createMock(HttpRequest::class),
                    $this->createMock(HttpResponse::class),
                    null,
                    null,
                    123,
                    new DateTime('now')
                ),
            ])
        );

        $this->assertSame($initialUris, $crawlerResult->getInitialUris());
        $this->assertSame($allowedHosts, $crawlerResult->getAllowedHosts());
        $this->assertSame($recursive, $crawlerResult->isRecursive());
        $this->assertSame($httpSuites, $crawlerResult->getHttpSuites());
        $this->assertSame($httpSuites->count(), $crawlerResult->count());
        $this->assertInstanceOf(\Iterator::class, $crawlerResult->getIterator());
    }
}
