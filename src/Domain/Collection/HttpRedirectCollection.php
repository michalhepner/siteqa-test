<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Domain\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use DomainException;
use IteratorAggregate;
use Siteqa\App\Test\Domain\Model\HttpRedirect;

class HttpRedirectCollection implements IteratorAggregate, Countable, ArrayAccess
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

    public function add(HttpRedirect $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function remove(HttpRedirect $item): self
    {
        foreach ($this->items as $key => $tmpItem) {
            if ($tmpItem === $item) {
                unset($this->items[$key]);
            }
        }

        $this->items = array_values($this->items);

        return $this;
    }

    public function has(HttpRedirect $item): bool
    {
        foreach ($this->items as $key => $tmpItem) {
            if ($tmpItem === $item) {
                return true;
            }
        }

        return false;
    }

    public function first(): ?HttpRedirect
    {
        foreach ($this->items as $item) {
            return $item;
        }

        return null;
    }

    public function last(): ?HttpRedirect
    {
        $item = null;
        foreach ($this->items as $tmpItem) {
            $item = $tmpItem;
        }

        return $item;
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

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param mixed $offset
     *
     * @return HttpRedirect
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param mixed $offset
     * @param HttpRedirect $value
     *
     * @throws DomainException
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof HttpRedirect) {
            throw new DomainException(sprintf(
                'Argument 2 passed to %s must be an instance of %s, %s provided.',
                __METHOD__,
                HttpRedirect::class,
                gettype($value)
            ));
        }

        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
