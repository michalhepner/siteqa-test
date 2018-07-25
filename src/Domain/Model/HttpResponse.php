<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Siteqa\Test\Domain\Collection\HttpHeaderCollection;

class HttpResponse
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var HttpHeaderCollection
     */
    private $headers;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * @var string|null
     */
    private $reasonPhrase;

    /**
     * @var DateTime
     */
    private $created;

    public function __construct(
        int $statusCode,
        HttpHeaderCollection $headers,
        string $body,
        string $protocolVersion,
        ?string $reasonPhrase = null,
        ?DateTime $created = null
    ) {
        $this->setStatusCode($statusCode);
        $this->setHeaders($headers);
        $this->setBody($body);
        $this->setProtocolVersion($protocolVersion);
        $this->setReasonPhrase($reasonPhrase);
        $this->setCreated($created ?? new DateTime('now'));
    }

    public static function createFromPsrResponse(ResponseInterface $response): self
    {
        return new self(
            $response->getStatusCode(),
            HttpHeaderCollection::fromRawArray($response->getHeaders()),
            $response->getBody()->getContents(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    public function getHeaders(): HttpHeaderCollection
    {
        return $this->headers;
    }

    public function setHeaders(HttpHeaderCollection $headers): HttpResponse
    {
        $this->headers = $headers;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): HttpResponse
    {
        $this->body = $body;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): HttpResponse
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(string $protocolVersion): HttpResponse
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    public function getReasonPhrase(): ?string
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase(?string $reasonPhrase): HttpResponse
    {
        $reasonPhrase = trim((string) $reasonPhrase);
        $this->reasonPhrase = $reasonPhrase !== '' ? $reasonPhrase : null;

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): HttpResponse
    {
        $this->created = $created;

        return $this;
    }
}
