<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Provider;

use Exception;
use GuzzleHttp\Promise\FulfilledPromise;
use PHPUnit\Framework\TestCase;
use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\HttpRequest;
use Siteqa\Test\Domain\Model\HttpResponse;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\CrawlerResultProvider;
use Siteqa\Test\Provider\HttpSuiteProviderInterface;

class CrawlerResultProviderTest extends TestCase
{
    /**
     * @dataProvider provider
     * @param array $initialUris
     * @param array $allowedHosts
     * @param array $responseLinks
     * @param array $expectedUris
     * @param bool $recursive
     * @param array $uriFilters
     */
    public function testProvide(
        array $initialUris,
        array $allowedHosts,
        array $responseLinks,
        array $expectedUris,
        bool $recursive = true,
        array $uriFilters = []
    ): void {
        $uriSortingFunc = function (Uri $uri1, Uri $uri2) {
            return strcmp($uri1->__toString(), $uri2->__toString());
        };
        $uriMapFunc = function (Uri $uri) {
            return $uri->__toString();
        };

        $expectedUris = new UriCollection($expectedUris);
        $expectedUris->usort($uriSortingFunc);

        $httpSuiteProvider = $this->getMockBuilder(HttpSuiteProviderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['provideAsync', 'provideSync'])
            ->getMock()
        ;

        $httpSuiteProvider
            ->method('provideAsync')
            ->willReturnCallback(function (Uri $uri, callable $onFinish) use ($responseLinks) {
                $responseMock = $this->getMockBuilder(HttpResponse::class)
                    ->disableOriginalConstructor()
                    ->getMock()
                ;

                $uriString = $uri->__toString();
                if (!array_key_exists($uriString, $responseLinks)) {
                    throw new Exception('Unable to find response for '.$uriString);
                }

                $responseMock->method('getStatusCode')->willReturn(200);
                $body = $this->createHtmlWithLinks($responseLinks[$uriString]);
                $responseMock->method('getBody')->willReturn($body);

                $httpSuite = new HttpSuite(
                    $uri,
                    $this->createMock(HttpRequest::class),
                    $responseMock,
                    null,
                    null,
                    100
                );

                $onFinish($httpSuite);

                return new FulfilledPromise($httpSuite);
            })
        ;

        /** @var HttpSuiteProviderInterface $httpSuiteProvider */
        $crawlerResultProvider = new CrawlerResultProvider($httpSuiteProvider);
        $crawlerResultProvider
            ->setUriFilters($uriFilters)
            ->setRecursive($recursive)
        ;
        $crawlerResult = $crawlerResultProvider->provide($initialUris, $allowedHosts);

        $actualUris = new UriCollection();
        foreach ($crawlerResult->getHttpSuites() as $httpSuite) {
            $actualUris->add($httpSuite->getUri());
        }

        $actualUris->usort($uriSortingFunc);


        $this->assertEquals(
            $expectedUris->map($uriMapFunc),
            $actualUris->map($uriMapFunc)
        );
    }

    public function provider(): array
    {
        return [
            [
                ['https://example.com/page1', 'https://example.com/page2'],
                ['example.com'],
                [
                    'https://example.com/page1' => [
                        'https://example.com/page1',
                        'https://example.com/page3',
                    ],
                    'https://example.com/page2' => [
                        'https://example.com/page4',
                        'https://example.com/page5',
                    ],
                    'https://example.com/page3' => [],
                    'https://example.com/page4' => [],
                    'https://example.com/page5' => [],
                ],
                [
                    'https://example.com/page1',
                    'https://example.com/page2',
                    'https://example.com/page3',
                    'https://example.com/page4',
                    'https://example.com/page5',
                ],
            ],
            [
                ['https://example.com/page1', 'https://example.com/page2'],
                ['www.example.com'],
                [],
                [],
            ],
            [
                ['https://www.example.com/page1', 'http://example.com/page2'],
                ['example.com'],
                [
                    'http://example.com/page2' => [],
                ],
                [
                    'http://example.com/page2',
                ],
            ],
            [
                ['https://www.example.com/page1'],
                ['www.example.com'],
                [
                    'https://www.example.com/page1' => [],
                ],
                [
                    'https://www.example.com/page1',
                ],
            ],
            [
                ['https://example.com/page1'],
                ['example.com'],
                [
                    'https://example.com/page1' => [
                        'https://example.com/page2',
                    ],
                ],
                [
                    'https://example.com/page1',
                ],
                false
            ],
            [
                ['https://example.com/page1', 'https://example.com/page2'],
                ['example.com'],
                [
                    'https://example.com/page1' => [
                        'https://example.com/page3',
                        'https://example.com/page4',
                    ],
                    'https://example.com/page4' => [
                        'https://example.com/page5'
                    ],
                    'https://example.com/page5' => []
                ],
                [
                    'https://example.com/page1',
                    'https://example.com/page4',
                    'https://example.com/page5',
                ],
                true,
                [
                    function (Uri $uri) {
                        return !in_array($uri->__toString(), ['https://example.com/page2', 'https://example.com/page3'], true);
                    }
                ]
            ],
        ];
    }

    protected function createHtmlWithLinks(array $hrefs = []): string
    {
        return sprintf(
            '<html><head><title>Foo</title></head><body>%s</body></html>',
            implode('', array_map(
                function (string $href) {
                    return sprintf('<a href="%s">Bar</a>', $href);
                },
                $hrefs
            ))
        );
    }
}
