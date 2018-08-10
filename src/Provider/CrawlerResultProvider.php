<?php

declare(strict_types = 1);

namespace Siteqa\Test\Provider;

use Exception;
use GuzzleHttp\Promise\EachPromise;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Siteqa\Test\Domain\Collection\HttpSuiteCollection;
use Siteqa\Test\Domain\Collection\UriCollection;
use Siteqa\Test\Domain\Model\CrawlerResult;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Event\CrawlerUriQueuedEvent;
use Siteqa\Test\Event\EventDispatcherAwareInterface;
use Siteqa\Test\Event\EventDispatcherAwareTrait;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerResultProvider implements LoggerAwareInterface, EventDispatcherAwareInterface
{
    use LoggerAwareTrait, EventDispatcherAwareTrait;

    /**
     * @var HttpSuiteProviderInterface
     */
    protected $httpSuiteProvider;

    /**
     * @var int
     */
    protected $poolSize = 15;

    /**
     * @var bool
     */
    protected $recursive = false;

    /**
     * @var bool
     */
    protected $ignoreScheme = true;

    /**
     * @var bool
     */
    protected $ignoreFragment = true;

    /**
     * @var bool
     */
    protected $caseSensitive = false;

    /**
     * @var callable[]
     */
    protected $uriFilters = [];

    public function __construct(
        HttpSuiteProviderInterface $httpSuiteProvider,
        array $uriFilters = [],
        $recursive = false,
        int $poolSize = 15
    ) {
        $this->httpSuiteProvider = $httpSuiteProvider;
        $this->uriFilters = $uriFilters;
        $this->poolSize = $poolSize;
        $this->recursive = $recursive;
    }

    /**
     * @param UriCollection|Uri[] $initialUris
     * @param string[] $allowedHosts
     * @return CrawlerResult
     */
    public function provide($initialUris, array $allowedHosts): CrawlerResult
    {
        if (is_array($initialUris)) {
            $initialUris = new UriCollection($initialUris);
        } elseif (!$initialUris instanceof UriCollection) {
            throw new InvalidArgumentException('Argument 1 passed to '.__METHOD__.' must be either an array of an instance of '.UriCollection::class);
        }

        $pending = $initialUris->copy();
        $pending = $pending->filter(function (Uri $uri) use ($allowedHosts) {
            if (!in_array($uri->getHost(), $allowedHosts)) {
                return false;
            }

            foreach ($this->uriFilters as $uriFilter) {
                if ($uriFilter($uri) === false) {
                    return false;
                }
            }

            return true;
        });

        foreach ($pending as $uri) {
            $this->dispatch(new CrawlerUriQueuedEvent($uri));
        }

        $running = new UriCollection();
        $finished = new UriCollection();

        $httpSuites = new HttpSuiteCollection();

        $flags = [];
        !$this->caseSensitive && $flags[] = UriCollection::CASE_INSENSITIVE;
        $this->ignoreScheme && $flags[] = UriCollection::IGNORE_SCHEME;
        $this->ignoreFragment && $flags[] = UriCollection::IGNORE_FRAGMENT;

        $onFinish = function (HttpSuite $suite) use ($httpSuites, $pending, $running, $finished, $allowedHosts, $flags) {
            $running->removeString($suite->getRequest()->getUri()->__toString());
            $finished->push($suite->getRequest()->getUri());

            $httpSuites->add($suite);

            if ($this->recursive) {
                foreach ($this->getResponseUris($suite, $allowedHosts) as $uri) {
                    $uriString = $uri->__toString();

                    if (!$pending->hasString($uriString, $flags) &&
                        !$running->hasString($uriString, $flags) &&
                        !$finished->hasString($uriString, $flags) &&
                        in_array($uri->getHost(), $allowedHosts, true)) {
                        $this->dispatch(new CrawlerUriQueuedEvent($uri));
                        $pending->push($uri);
                    }
                }
            }
        };

        while (!$pending->isEmpty()) {
            $promises = [];
            while (!$pending->isEmpty() && $running->count() < $this->poolSize) {
                $item = $pending->shift();
                $running->add($item);
                $promises[] = $this->httpSuiteProvider->provideAsync($item, $onFinish);
            }
            (new EachPromise($promises))->promise()->wait();
        }

        return new CrawlerResult($initialUris, $allowedHosts, $this->recursive, $httpSuites);
    }

    public function getPoolSize(): int
    {
        return $this->poolSize;
    }

    public function setPoolSize(int $poolSize): self
    {
        $this->poolSize = $poolSize;

        return $this;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

    public function setRecursive(bool $recursive): self
    {
        $this->recursive = $recursive;

        return $this;
    }

    public function getUriFilters(): array
    {
        return $this->uriFilters;
    }

    public function setUriFilters(array $uriFilters): self
    {
        $this->uriFilters = [];
        foreach ($uriFilters as $uriFilter) {
            $this->addUriFilter($uriFilter);
        }

        return $this;
    }

    public function addUriFilter(callable $uriFilter): self
    {
        $this->uriFilters[] = $uriFilter;

        return $this;
    }

    public function getIgnoreScheme(): bool
    {
        return $this->ignoreScheme;
    }

    public function setIgnoreScheme(bool $ignoreScheme): self
    {
        $this->ignoreScheme = $ignoreScheme;

        return $this;
    }

    public function getIgnoreFragment(): bool
    {
        return $this->ignoreFragment;
    }

    public function setIgnoreFragment(bool $ignoreFragment): self
    {
        $this->ignoreFragment = $ignoreFragment;

        return $this;
    }

    public function getCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $caseSensitive): self
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    protected function getResponseUris(HttpSuite $httpSuite, array $allowedHosts): UriCollection
    {
        $uriCollection = new UriCollection();

        if ($httpSuite->hasResponse()) {
            $crawler = new Crawler($httpSuite->getResponse()->getBody());
            try {
                $crawler->filter('*')->each(function (Crawler $node) use ($uriCollection, $httpSuite, $allowedHosts) {
                    if ($node->nodeName() === 'a' && ($href = trim((string) $node->attr('href'))) !== '') {

                        $hrefUri = null;

                        try {
                            $hrefUri = Uri::createFromString($href);
                        } catch (Exception $exception) {
                        }

                        if ($hrefUri !== null && in_array(trim((string) $hrefUri->getScheme()), ['', 'http', 'https'], true)) {
                            $redirects = $httpSuite->getRedirects();
                            $request = $httpSuite->getRequest();
                            $lastHost = $redirects->isEmpty() ? $request->getUri()->getHost() : $redirects->last()->getTo()->getUri()->getHost();

                            !$hrefUri->hasHost() && $hrefUri = $hrefUri->withHost($lastHost);
                            trim((string) $hrefUri->getScheme()) === '' && $hrefUri = $hrefUri->withScheme($request->getUri()->getScheme());

                            if (!in_array($hrefUri->getHost(), $allowedHosts)) {
                                return;
                            }

                            foreach ($this->uriFilters as $uriFilter) {
                                if ($uriFilter($hrefUri) === false) {
                                    return;
                                }
                            }

                            $uriCollection->add($hrefUri);
                        }
                    }
                });
            } catch (Exception $exception) {
            }
        }

        return $uriCollection;
    }
}
