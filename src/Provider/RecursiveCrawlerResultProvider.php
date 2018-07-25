<?php

declare(strict_types = 1);

namespace Siteqa\Test\Provider;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\CrawlerResult;

class RecursiveCrawlerResultProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var CrawlerResultProvider
     */
    protected $crawlerResultProvider;

    public function __construct(HttpSuiteProviderInterface $httpSuiteProvider, array $uriFilters = [], int $poolSize = 15)
    {
        $this->crawlerResultProvider = new CrawlerResultProvider($httpSuiteProvider, $uriFilters, true, $poolSize);
    }

    public function provide(UriCollection $initialUris, array $allowedHosts = []): CrawlerResult
    {
        return $this->crawlerResultProvider->provide($initialUris, $allowedHosts);
    }

    public function getPoolSize(): int
    {
        return $this->crawlerResultProvider->getPoolSize();
    }

    public function setPoolSize(int $poolSize): self
    {
        $this->crawlerResultProvider->setPoolSize($poolSize);

        return $this;
    }

    public function getUriFilters(): array
    {
        return $this->crawlerResultProvider->getUriFilters();
    }

    public function setUriFilters(array $uriFilters): self
    {
        $this->crawlerResultProvider->setUriFilters($uriFilters);

        return $this;
    }

    public function addUriFilter(callable $uriFilter): self
    {
        $this->crawlerResultProvider->addUriFilter($uriFilter);

        return $this;
    }
}
