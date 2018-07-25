<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Provider;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Siteqa\App\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\App\Test\Domain\Factory\HttpExceptionFactory;
use Siteqa\App\Test\Domain\Model\HttpRedirect;
use Siteqa\App\Test\Domain\Model\HttpRequest;
use Siteqa\App\Test\Domain\Model\HttpResponse;
use Siteqa\App\Test\Domain\Model\HttpSuite;
use Siteqa\App\Test\Domain\Model\Uri;
use Siteqa\App\Test\Event\EventDispatcherAwareInterface;
use Siteqa\App\Test\Event\EventDispatcherAwareTrait;
use Siteqa\App\Test\Event\HttpRequestEvent;
use Siteqa\App\Test\Event\HttpSuiteEvent;

class HttpSuiteProvider implements LoggerAwareInterface, HttpSuiteProviderInterface, EventDispatcherAwareInterface
{
    use LoggerAwareTrait, EventDispatcherAwareTrait;

    /**
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * @var int
     */
    protected $maxRedirects = 10;

    /**
     * Time in seconds after which the request should be aborted.
     *
     * @var int
     */
    protected $timeout = 60;

    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function provideSync(Uri $uri): HttpSuite
    {
        $httpSuite = null;
        $onFinish = function (HttpSuite $tmp) use (&$httpSuite) {
            $httpSuite = $tmp;
        };

        $this->provideAsync($uri, $onFinish)->wait();

        return $httpSuite;
    }

    public function provideAsync(Uri $uri, callable $onFinish): PromiseInterface
    {
        $httpRequest = new HttpRequest($uri);
        $httpRedirects = new HttpRedirectCollection();

        $options = $this->getRequestOptions($httpRequest, $httpRedirects);
        $startTime = microtime(true);

        $this->dispatch(new HttpRequestEvent($httpRequest));

        $promise = $this->guzzle->requestAsync('get', $uri->withUserInfo('', '')->__toString(), $options);

        $promise->then(
            function (ResponseInterface $internalResponse) use ($uri, $onFinish, $httpRequest, $httpRedirects, $startTime) {
                $time = (int) round((microtime(true) - $startTime) * 1000);
                $httpResponse = HttpResponse::createFromPsrResponse($internalResponse);
                $httpSuite = new HttpSuite($uri, $httpRequest, $httpResponse, null, $httpRedirects, $time);
                $this->dispatch(new HttpSuiteEvent($httpSuite));
                $onFinish($httpSuite);
            },
            function (RequestException $exception) use ($uri, $onFinish, $httpRequest, $httpRedirects, $startTime) {
                $time = (int) round((microtime(true) - $startTime) * 1000);
                $internalResponse = $exception->getResponse();
                $httpResponse = $internalResponse ? HttpResponse::createFromPsrResponse($internalResponse) : null;
                $httpException = HttpExceptionFactory::createFromGuzzleHandlerContext($exception->getHandlerContext());
                $httpSuite = new HttpSuite($uri, $httpRequest, $httpResponse, $httpException, $httpRedirects, $time);
                $this->dispatch(new HttpSuiteEvent($httpSuite));
                $onFinish($httpSuite);
            }
        );

        return $promise;
    }

    public function getMaxRedirects(): int
    {
        return $this->maxRedirects;
    }

    public function setMaxRedirects(int $maxRedirects): self
    {
        $this->maxRedirects = $maxRedirects;

        return $this;
    }

    public function setTimeout(int $timeout): HttpSuiteProvider
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    protected function getRequestOptions(HttpRequest $httpRequest, HttpRedirectCollection $httpRedirects): array
    {
        $options = [
            'allow_redirects' => [
                'max' => $this->maxRedirects,
                'strict' => true,
                'referer' => true,
                'protocols' => [
                    HttpRequest::SCHEME_HTTP,
                    HttpRequest::SCHEME_HTTPS
                ],
                'on_redirect' => $this->onRedirect($httpRequest, $httpRedirects),
                'track_redirects' => true,
            ],
            'http_errors' => false,
            'timeout' => $this->timeout,
        ];

        if ($httpRequest->getUri()->getUserInfo()) {
            $options['auth'] = [
                $httpRequest->getUri()->getUser(),
                $httpRequest->getUri()->getPass()
            ];
        }

        return $options;
    }

    protected function onRedirect(HttpRequest $httpRequest, HttpRedirectCollection $httpRedirects): callable
    {
        return function (RequestInterface $tmpRequest, ResponseInterface $tmpResponse, UriInterface $uri) use ($httpRequest, $httpRedirects) {
            $httpRedirects->add(new HttpRedirect(
                $httpRedirects->last() ? $httpRedirects->last()->getTo() : $httpRequest,
                new HttpRequest(Uri::createFromString($uri->__toString()))
            ));
        };
    }
}
