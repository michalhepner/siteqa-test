<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use PHPUnit\Framework\TestCase;
use Siteqa\App\Test\Domain\Collection\HttpHeaderCollection;
use Siteqa\App\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\App\Test\Domain\Collection\UriCollection;
use Siteqa\App\Test\Domain\Factory\HttpExceptionFactory;
use Siteqa\App\Test\Domain\Model\HttpRequest;
use Siteqa\App\Test\Domain\Model\HttpResponse;
use Siteqa\App\Test\Domain\Model\HttpSuite;
use Siteqa\App\Test\Domain\Model\Sitemap;
use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Domain\Model\Uri;

abstract class AbstractTestTest extends TestCase
{
    protected function createHttpSuite(
        ?int $responseCode,
        ?string $responseBody,
        ?array $exceptionContext,
        ?string $uri = null,
        ?int $duration = null,
        ?HttpRedirectCollection $redirects = null
    ): HttpSuite {

        $uri = Uri::createFromString($uri ?? 'https://example.com');
        $duration = $duration ?? 1000;
        $redirects = $redirects ?? new HttpRedirectCollection();

        $request = new HttpRequest($uri);
        $response = $responseCode !== null && $responseBody !== null ? new HttpResponse($responseCode, new HttpHeaderCollection(), $responseBody, '1.1') : null;
        $exception = $exceptionContext !== null ? HttpExceptionFactory::createFromGuzzleHandlerContext($exceptionContext) : null;

        return new HttpSuite($uri, $request, $response, $exception, $redirects, $duration);
    }

    protected function createSitemapResult(
        string $uri = null,
        array $uris,
        ?int $responseCode,
        ?string $responseBody,
        ?array $exceptionContext,
        ?int $duration = null,
        ?HttpRedirectCollection $redirects = null
    ) {
        $httpSuite = $this->createHttpSuite($responseCode, $responseBody, $exceptionContext, $uri, $duration, $redirects);

        return new SitemapResult(
            new Sitemap($httpSuite->getUri()),
            new UriCollection($uris),
            $httpSuite
        );
    }
}
