<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Domain\Collection;

use PHPUnit\Framework\TestCase;
use Siteqa\App\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\App\Test\Domain\Collection\HttpSuiteCollection;
use Siteqa\App\Test\Domain\Model\HttpRequest;
use Siteqa\App\Test\Domain\Model\HttpResponse;
use Siteqa\App\Test\Domain\Model\HttpSuite;
use Siteqa\App\Test\Domain\Model\Uri;

class HttpSuiteCollectionTest extends TestCase
{
    public function test(): void
    {
        $testSuiteFactory = function (string $uri) {
            return HttpSuite::createWithResponse(
                Uri::createFromString($uri),
                $this->createMock(HttpRequest::class),
                $this->createMock(HttpResponse::class),
                $this->createMock(HttpRedirectCollection::class),
                1000
            );
        };

        /**
         * @var HttpSuite $testSuite1
         * @var HttpSuite $testSuite2
         * @var HttpSuite $testSuite3
         * @var HttpSuite $testSuite4
         */
        $items = [
            $testSuite1 = $testSuiteFactory('https://example.com/page1'),
            $testSuite3 = $testSuiteFactory('https://example.com/page3'),
            $testSuite2 = $testSuiteFactory('https://example.com/page2'),
        ];

        $testSuite4 = $testSuiteFactory('https://example.com/page4');

        $collection = new HttpSuiteCollection($items);

        $this->assertSame($testSuite1, $collection->first());
        $this->assertSame($testSuite2, $collection->last());

        $this->assertEquals(count($items), $collection->count());

        $this->assertTrue($collection->has($testSuite1));
        $this->assertTrue($collection->has($testSuite2));
        $this->assertTrue($collection->has($testSuite3));

        $collection->remove($testSuite2);

        $this->assertFalse($collection->has($testSuite2));

        $collection->add($testSuite4);
        $this->assertTrue($collection->has($testSuite4));

        $collection->usort(function (HttpSuite $httpSuite1, HttpSuite $httpSuite2) {
            return strcmp(
                $httpSuite2->getUri()->__toString(),
                $httpSuite1->getUri()->__toString()
            );
        });

        $this->assertSame($testSuite4, $collection->first());
        $this->assertSame($testSuite1, $collection->last());

        $this->assertEquals(3, $collection->count());
        $this->assertFalse($collection->isEmpty());

        foreach ($collection as $item) {
            $collection->remove($item);
        }

        $this->assertTrue($collection->isEmpty());
    }
}
