<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use Countable;
use IteratorAggregate;
use Siteqa\Test\Domain\Collection\HttpSuiteCollection;
use Siteqa\Test\Domain\Collection\UriCollection;

class CrawlerResult implements Countable, IteratorAggregate
{
    /**
     * @var UriCollection
     */
    protected $initialUris;

    /**
     * @var string[]
     */
    protected $allowedHosts;

    /**
     * @var bool
     */
    protected $recursive;

    /**
     * @var HttpSuiteCollection
     */
    protected $httpSuites;

    public function __construct(UriCollection $initialUris, array $allowedHosts, bool $recursive, HttpSuiteCollection $httpSuites)
    {
        $this->initialUris = $initialUris;
        $this->httpSuites = $httpSuites;
        $this->allowedHosts = $allowedHosts;
        $this->recursive = $recursive;
    }

    public function getInitialUris(): UriCollection
    {
        return $this->initialUris;
    }

    public function getHttpSuites(): HttpSuiteCollection
    {
        return $this->httpSuites;
    }

    public function getAllowedHosts(): array
    {
        return $this->allowedHosts;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

    public function count()
    {
        return $this->httpSuites->count();
    }

    public function getIterator()
    {
        return $this->httpSuites->getIterator();
    }
}
