<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Siteqa\Test\Domain\Model\HttpSuite;

class HttpSuiteCollection implements IteratorAggregate, Countable
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

    public function add(HttpSuite $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function remove(HttpSuite $item): self
    {
        foreach ($this->items as $key => $tmpItem) {
            if ($tmpItem === $item) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    public function has(HttpSuite $item): bool
    {
        foreach ($this->items as $key => $tmpItem) {
            if ($tmpItem === $item) {
                return true;
            }
        }

        return false;
    }

    public function first(): ?HttpSuite
    {
        foreach ($this->items as $item) {
            return $item;
        }

        return null;
    }

    public function last(): ?HttpSuite
    {
        $item = null;
        foreach ($this->items as $tmpItem) {
            $item = $tmpItem;
        }

        return $item;
    }

    public function usort(callable $func): void
    {
        usort($this->items, $func);
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
}
