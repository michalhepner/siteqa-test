<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Domain\Collection;

use PHPUnit\Framework\TestCase;
use Siteqa\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\Test\Domain\Model\HttpRedirect;

class HttpRedirectCollectionTest extends TestCase
{
    public function test1(): void
    {
        $redirectFactory = function () {
            return $this
                ->getMockBuilder(HttpRedirect::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
        };

        /**
         * @var HttpRedirect $redirect1
         * @var HttpRedirect $redirect2
         * @var HttpRedirect $redirect3
         * @var HttpRedirect $redirect4
         */
        $redirect1 = $redirectFactory();
        $redirect2 = $redirectFactory();
        $redirect3 = $redirectFactory();
        $redirect4 = $redirectFactory();

        $collection = new HttpRedirectCollection([
            $redirect1,
            $redirect2
        ]);

        $this->assertSame($redirect1, $collection->first());
        $this->assertSame($redirect2, $collection->last());

        $collection->add($redirect3);
        $this->assertSame($redirect3, $collection->last());

        $this->assertEquals(3, $collection->count());
        $this->assertFalse($collection->isEmpty());

        $this->assertTrue($collection->has($redirect2));

        $collection->remove($redirect1);
        $this->assertEquals(2, $collection->count());
        $this->assertSame($redirect2, $collection->first());
        $this->assertSame($redirect3, $collection->last());

        $this->assertTrue($collection->offsetExists(0));
        $this->assertTrue($collection->offsetExists(1));
        $this->assertFalse($collection->offsetExists(2));

        $this->assertSame($redirect3, $collection->offsetGet(1));
        $collection->offsetSet(0, $redirect4);

        $this->assertSame($redirect4, $collection->first());
    }
}
