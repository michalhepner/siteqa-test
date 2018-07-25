<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Domain\Collection;

use PHPUnit\Framework\TestCase;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Domain\Collection\UriCollection;

class UriCollectionTest extends TestCase
{
    public function test1(): void
    {
        $collection = new UriCollection([
            $uri1 = Uri::createFromString('https://example.com/page1'),
            $uri2 = Uri::createFromString('https://example.com/page4'),
            $uri3 = Uri::createFromString('https://example.com/Page2'),
            $uri4 = Uri::createFromString('https://example.com/pAge3'),
            $uri5 = Uri::createFromString('https://example.com/Page4'),
            $uri6 = Uri::createFromString('https://example.com/page4'),
        ]);

        $this->assertSame($uri1, $collection->first());
        $this->assertSame($uri6, $collection->last());
        $this->assertFalse($collection->isEmpty());
        $this->assertSame($uri6, $collection->pop());
        $this->assertSame($uri5, $collection->last());
        $this->assertEquals(5, $collection->count());
        $this->assertNotSame($collection, $collection->copy());
        $this->assertTrue($collection->hasString('https://example.com/Page2'));
        $this->assertTrue($collection->hasString('https://example.com/page2', [UriCollection::CASE_INSENSITIVE]));
        $this->assertFalse($collection->hasString('https://example.com/page2'));
        $collection->add($uri6);
        $this->assertEquals(1, $collection->duplicated()->count());
        $this->assertEquals(2, $collection->duplicated([UriCollection::CASE_INSENSITIVE])->count());
        $this->assertSame($uri1, $collection->shift());

        $this->assertEquals(
            [
                'https://example.com/page4',
                'https://example.com/Page2',
                'https://example.com/pAge3',
                'https://example.com/Page4',
                'https://example.com/page4',
            ],
            $collection->map(function (Uri $uri) {
                return $uri->__toString();
            })
        );

        $this->assertEquals(
            [
                'https://example.com/page4',
                'https://example.com/page4',
            ],
            $collection->filter(function (Uri $uri) {
                return strtolower($uri->__toString()) === $uri->__toString();
            })->map(function (Uri $uri) {
                return $uri->__toString();
            })
        );

        $collection->usort(function (Uri $uri1, Uri $uri2) {
            return strcasecmp($uri1->__toString(), $uri2->__toString());
        });

        $this->assertEquals(
            [
                'https://example.com/Page2',
                'https://example.com/pAge3',
                'https://example.com/page4',
                'https://example.com/Page4',
                'https://example.com/page4',
            ],
            $collection->map(function (Uri $uri) {
                return $uri->__toString();
            })
        );
    }
}
