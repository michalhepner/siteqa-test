<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Provider;

use PHPUnit\Framework\TestCase;
use Siteqa\Test\Domain\Collection\HttpHeaderCollection;
use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\HttpResponse;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Sitemap;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\HttpSuiteProviderInterface;
use Siteqa\Test\Provider\SitemapResultProvider;

class SitemapResultProviderTest extends TestCase
{
    /**
     * @dataProvider provider
     * @param string $body
     * @param UriCollection $expectedUris
     */
    public function testProvide(string $body, UriCollection $expectedUris): void
    {
        $httpSuiteProviderMock = $this->getMockBuilder(HttpSuiteProviderInterface::class)
            ->setMethods(['provideSync', 'provideAsync'])
            ->getMock()
        ;

        $httpSuiteMock = $this->getMockBuilder(HttpSuite::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResponse'])
            ->getMock()
        ;

        $httpSuiteMock
            ->method('getResponse')
            ->willReturn(new HttpResponse(200, new HttpHeaderCollection(), $body, '1.1'))
        ;

        $httpSuiteProviderMock
            ->method('provideSync')
            ->willReturn($httpSuiteMock)
        ;

        /** @var HttpSuiteProviderInterface $httpSuiteProviderMock */

        $provider = new SitemapResultProvider($httpSuiteProviderMock);
        $sitemapResult = $provider->provide(new Sitemap(Uri::createFromString()));

        $this->assertSame($expectedUris->count(), $sitemapResult->getUris()->count());
        $this->assertEquals(
            $expectedUris->map(function (Uri $uri) {
                return $uri->__toString();
            }),
            $sitemapResult->getUris()->map(function (Uri $uri) {
                return $uri->__toString();
            })
        );
    }

    public function provider(): array
    {
        return [
            [
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>https://phpunit.de/index.html</loc></url><url><loc>https://phpunit.de/build-status.html</loc></url><url><loc>https://phpunit.de/build-status.html</loc></url></urlset>',
                new UriCollection([
                    Uri::createFromString('https://phpunit.de/index.html'),
                    Uri::createFromString('https://phpunit.de/build-status.html'),
                    Uri::createFromString('https://phpunit.de/build-status.html'),
                ])
            ]
        ];
    }
}
