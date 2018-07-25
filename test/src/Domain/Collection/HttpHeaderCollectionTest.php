<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Domain\Collection;

use PHPUnit\Framework\TestCase;
use Siteqa\App\Test\Domain\Collection\HttpHeaderCollection;
use Siteqa\App\Test\Domain\Model\HttpHeader;

class HttpHeaderCollectionTest extends TestCase
{
    public function test1(): void
    {
        $items = [
            $contentTypeHeader = new HttpHeader('Content-Type', ['application/xhtml+xml; charset=utf-8']),
            $acceptCharsetHeader = new HttpHeader('Accept-Charset', ['utf-8'])
        ];

        $collection = new HttpHeaderCollection($items);
        $arrayCopy = array_values($collection->getIterator()->getArrayCopy());

        for ($i = 0; $i < count($items); $i++) {
            $this->assertSame($items[$i], $arrayCopy[$i]);
        }

        $connectionHeader = new HttpHeader('Connection', ['close']);
        $collection->add($connectionHeader);

        $this->assertTrue($collection->has($connectionHeader));
        $this->assertEquals(count($items) + 1, $collection->count());
        $this->assertSame($connectionHeader, $collection->get($connectionHeader->getName()));

        $collection->remove($connectionHeader);
        $this->assertFalse($collection->has($connectionHeader));
        $this->assertEquals(count($items), $collection->count());

        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->offsetExists($contentTypeHeader->getName()));
        $this->assertFalse($collection->offsetExists($connectionHeader->getName()));

        $this->assertEquals($items, $collection->toArray());
    }

    public function test2(): void
    {
        $input = [
            'Content-Type' => [
                'application/xhtml+xml; charset=utf-8',
            ],
            'Connection' => [
                'close',
            ]
        ];

        $collection = HttpHeaderCollection::fromRawArray($input);

        $this->assertTrue($collection->offsetExists('Content-Type'));
        $this->assertTrue($collection->offsetExists('Connection'));
        $this->assertFalse($collection->offsetExists('Accept-Charset'));

        $this->assertEquals(['close'], $collection->get('Connection')->getValues());
    }
}
