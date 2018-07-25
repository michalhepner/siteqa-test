<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Siteqa\Test\Domain\Model\HttpHeader;

class HttpHeaderCollection implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(HttpHeader $item): self
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    public function remove(HttpHeader $item): self
    {
        unset($this->items[$item->getName()]);

        return $this;
    }

    public function has(HttpHeader $item): bool
    {
        return array_key_exists($item->getName(), $this->items);
    }

    public function get(string $name): HttpHeader
    {
        return $this->items[$name];
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param string $offset
     *
     * @return HttpHeader
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @param array $values
     */
    public function offsetSet($offset, $values)
    {
        $this->items[$offset] = new HttpHeader($offset, $values);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function toArray(): array
    {
        return array_values($this->items);
    }

    public function toRawArray(): array
    {
        $tmpItems = $this->items;
        array_walk($tmpItems, function (HttpHeader &$item) {
            $item = $item->getValues();
        });

        return $tmpItems;
    }

    public static function fromRawArray(array $rawArray): self
    {
        $headers = [];
        foreach ($rawArray as $headerName => $headerValues) {
            $headers[] = new HttpHeader($headerName, $headerValues);
        }

        return new self($headers);
    }
}
