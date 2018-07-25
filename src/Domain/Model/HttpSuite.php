<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use DateTime;
use Siteqa\Test\Domain\Collection\HttpRedirectCollection;

class HttpSuite
{
    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var HttpRedirectCollection
     */
    protected $redirects;

    /**
     * @var null|HttpResponse
     */
    protected $response;

    /**
     * @var null|HttpException
     */
    protected $exception;

    /**
     * Request duration in milliseconds.
     *
     * @var int
     */
    protected $duration;

    /**
     * @var DateTime
     */
    protected $created;

    public function __construct(
        Uri $uri,
        HttpRequest $request,
        ?HttpResponse $response,
        ?HttpException $exception,
        ?HttpRedirectCollection $redirects,
        int $duration,
        ?DateTime $created = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
        $this->redirects = $redirects ?? new HttpRedirectCollection();
        $this->duration = $duration;
        $this->uri = $uri;
        $this->created = $created ?? new DateTime('now');
    }

    public static function createWithResponse(Uri $uri, HttpRequest $request, HttpResponse $response, ?HttpRedirectCollection $redirectCollection, int $duration): self
    {
        return new self($uri, $request, $response, null, $redirectCollection, $duration);
    }

    public static function createWithException(Uri $uri, HttpRequest $request, HttpException $exception, ?HttpRedirectCollection $redirectCollection, int $duration): self
    {
        return new self($uri, $request, null, $exception, $redirectCollection, $duration);
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    public function hasRedirects(): bool
    {
        return !$this->redirects->isEmpty();
    }

    public function addRedirect(HttpRedirect $redirect): self
    {
        $this->redirects->add($redirect);

        return $this;
    }

    public function getRedirects(): HttpRedirectCollection
    {
        return $this->redirects;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    public function getResponse(): ?HttpResponse
    {
        return $this->response;
    }

    public function hasException(): bool
    {
        return $this->exception !== null;
    }

    public function getException(): ?HttpException
    {
        return $this->exception;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
