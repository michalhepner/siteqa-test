<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Collection;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Siteqa\Test\Domain\Model\Uri;

class UriCollection implements IteratorAggregate, Countable
{
    const IGNORE_SCHEME = 0;
    const IGNORE_HOST = 1;
    const IGNORE_FRAGMENT = 2;
    const CASE_INSENSITIVE = 4;

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

    /**
     * @param Uri|string $item
     * @return UriCollection
     */
    public function add($item): self
    {
        if (is_string($item)) {
            $item = Uri::createFromString($item);
        } elseif (!$item instanceof Uri) {
            throw new InvalidArgumentException('Uri needs to be either a string or an instance of '.Uri::class);
        }

        $this->items[] = $item;

        return $this;
    }

    public function remove(Uri $item): self
    {
        foreach ($this->items as $key => $tmpItem) {
            if ($tmpItem === $item) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    public function removeString(string $uri): int
    {
        $removeCount = 0;

        /** @var Uri $item */
        foreach ($this->items as $key => $item) {
            if ($item->__toString() === $uri) {
                unset($this->items[$key]);
                $removeCount++;
            }
        }

        return $removeCount;
    }

    public function has(Uri $item): bool
    {
        foreach ($this->items as $tmpItem) {
            if ($tmpItem->__toString() === $item->__toString()) {
                return true;
            }
        }

        return false;
    }

    public function hasString(string $uri, array $flags = []): bool
    {
        if (count($flags)) {
            $uriObj = Uri::createFromString($uri);
            $uriObj = in_array(self::IGNORE_SCHEME, $flags, true) ? $uriObj->withScheme('') : $uriObj;
            $uriObj = in_array(self::IGNORE_HOST, $flags, true) ? $uriObj->withHost('') : $uriObj;
            $uriObj = in_array(self::IGNORE_FRAGMENT, $flags, true) ? $uriObj->withFragment('') : $uriObj;

            $uri = $uriObj->__toString();
        }

        $uri = in_array(self::CASE_INSENSITIVE, $flags, true) ? strtolower($uri) : $uri;

        /** @var Uri $item */
        foreach ($this->items as $item) {
            if (count($flags)) {
                $item = in_array(self::IGNORE_SCHEME, $flags, true) ? $item->withScheme('') : $item;
                $item = in_array(self::IGNORE_HOST, $flags, true) ? $item->withHost('') : $item;
                $item = in_array(self::IGNORE_FRAGMENT, $flags, true) ? $item->withFragment('') : $item;
            }

            $itemString = $item->__toString();
            $itemString = in_array(self::CASE_INSENSITIVE, $flags, true) ? strtolower($itemString) : $itemString;

            if ($itemString === $uri) {
                return true;
            }
        }

        return false;
    }

    public function usort(callable $func): void
    {
        usort($this->items, $func);
    }

    public function first(): ?Uri
    {
        foreach ($this->items as $item) {
            return $item;
        }

        return null;
    }

    public function pop(): ?Uri
    {
        return count($this->items) ? array_pop($this->items) : null;
    }

    public function shift(): ?Uri
    {
        return count($this->items) ? array_shift($this->items) : null;
    }

    public function push(Uri $uri): self
    {
        array_push($this->items, $uri);

        return $this;
    }

    public function filter(callable $callable)
    {
        return new self(array_filter($this->items, $callable));
    }

    public function last(): ?Uri
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

    public function copy(): self
    {
        return new self($this->items);
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    public function duplicated(array $flags = []): self
    {
        $caseInsensitive = in_array(self::CASE_INSENSITIVE, $flags, true);

        $existingRaw = [];
        $duplicated = [];

        /** @var Uri $item */
        foreach ($this->items as $item) {
            $rawUri = $caseInsensitive ? strtolower(rawurldecode($item->__toString())) : rawurldecode($item->__toString());

            if (!array_key_exists($rawUri, $existingRaw)) {
                $existingRaw[$rawUri] = $item;
            } else {
                $duplicated[] = $item;
            }
        }

        return new self($duplicated);
    }
}
