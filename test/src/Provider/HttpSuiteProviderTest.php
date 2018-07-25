<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Provider;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Siteqa\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\Test\Domain\Factory\HttpExceptionFactory;
use Siteqa\Test\Domain\Model\HttpException;
use Siteqa\Test\Domain\Model\HttpResponse;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\HttpSuiteProvider;

class HttpSuiteProviderTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param Uri $uri
     * @param int $status
     * @param array $headers
     * @param null|string $body
     * @param null|string $exceptionMessage
     * @param null|array $handlerContext
     */
    public function testProvideSync(Uri $uri, ?int $status, ?array $headers = [], ?string $body = null, ?string $exceptionMessage = null, array $handlerContext = []): void
    {
        $requestException = $exceptionMessage !== null ? new RequestException(
            $exceptionMessage,
            $this->createMock(RequestInterface::class),
            $status || $headers || $body ? new Response($status, $headers, $body) : null,
            null,
            $handlerContext
        ) : null;

        $guzzleMock = $this->createGuzzleMock($uri, $status, $headers, $body, $requestException);

        $provider = new HttpSuiteProvider($guzzleMock);
        $httpSuite = $provider->provideSync($uri);

        $this->examineHttpSuite($httpSuite, $uri, $status, $headers, $body, $requestException);
    }

    /**
     * @dataProvider provider
     *
     * @param Uri $uri
     * @param int $status
     * @param array $headers
     * @param null|string $body
     * @param null|string $exceptionMessage
     * @param null|array $handlerContext
     */
    public function testProvideAsync(Uri $uri, ?int $status, ?array $headers = [], ?string $body = null, ?string $exceptionMessage = null, array $handlerContext = []): void
    {
        $requestException = $exceptionMessage !== null ? new RequestException(
            $exceptionMessage,
            $this->createMock(RequestInterface::class),
            $status || $headers || $body ? new Response($status, $headers, $body) : null,
            null,
            $handlerContext
        ) : null;

        $guzzleMock = $this->createGuzzleMock($uri, $status, $headers, $body, $requestException);

        $provider = new HttpSuiteProvider($guzzleMock);
        $provider->provideAsync($uri, function (HttpSuite $httpSuite) use ($uri, $status, $headers, $body, $requestException) {
            $this->examineHttpSuite($httpSuite, $uri, $status, $headers, $body, $requestException);
        });
    }

    public function provider()
    {
        return [
            [
                Uri::createFromString('https://example.com'),
                200,
                ['Connection' => ['keep-alive']],
                '<html><head><title>test</title></head><body>Hello world!</body></html>',
            ],
            [
                Uri::createFromString('https://example.com'),
                500,
                ['Connection' => ['keep-alive']],
                '',
                'Internal server error',
                [
                    'errno' => 1234,
                ],
            ],
            [
                Uri::createFromString('https://example.com'),
                null,
                null,
                null,
                'SSL exception',
                [
                    'errno' => 51,
                    'error' => 'SSL: no alternative certificate subject name matches target host name \'example.com\'',
                ],
            ],
        ];
    }

    protected function createGuzzleMock(Uri $uri, ?int $status = null, ?array $headers = [], ?string $body = null, ?RequestException $exception = null): Client
    {
        $promiseMock = $this->getMockBuilder(Promise::class)
            ->disableProxyingToOriginalMethods()
            ->getMock()
        ;

        $promiseMock
            ->method('then')
            ->willReturnCallback(function (callable $onFulfilled, callable $onRejected) use ($status, $headers, $body, $exception) {
                if (!$exception) {
                    $onFulfilled(new Response($status, $headers, $body));
                } else {
                    $onRejected($exception);
                }
            })
        ;

        $guzzleMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['requestAsync'])
            ->getMock()
        ;

        $guzzleMock
            ->method('requestAsync')
            ->with(
                $this->stringContains('get'),
                $this->stringContains($uri->__toString()),
                $this->isType('array')
            )
            ->willReturn($promiseMock)
        ;

        /** @var Client $guzzleMock */

        return $guzzleMock;
    }

    protected function examineHttpSuite(HttpSuite $httpSuite, Uri $uri, ?int $status = null, ?array $headers = [], ?string $body = null, ?RequestException $requestException = null)
    {
        $this->assertInstanceOf(HttpSuite::class, $httpSuite);
        $this->assertSame($uri, $httpSuite->getUri());
        $this->assertFalse($httpSuite->hasRedirects());
        $this->assertInstanceOf(HttpRedirectCollection::class, $httpSuite->getRedirects());
        $this->assertEquals(0, $httpSuite->getRedirects()->count());
        $this->assertInstanceOf(DateTime::class, $httpSuite->getCreated());
        $this->assertInstanceOf(DateTime::class, $httpSuite->getRequest()->getCreated());

        if (!$requestException || $requestException->hasResponse()) {
            $this->assertTrue($httpSuite->hasResponse());
            $this->assertInstanceOf(HttpResponse::class, $httpSuite->getResponse());
            $this->assertInstanceOf(DateTime::class, $httpSuite->getResponse()->getCreated());

            $this->assertEquals($status, $httpSuite->getResponse()->getStatusCode());
            $this->assertEquals(count($headers), $httpSuite->getResponse()->getHeaders()->count());

            foreach ($headers as $headerName => $headerValues) {
                $this->assertTrue($httpSuite->getResponse()->getHeaders()->offsetExists($headerName));
                $this->assertEquals($headerValues, $httpSuite->getResponse()->getHeaders()->get($headerName)->getValues());
            }

            $this->assertEquals($body, $httpSuite->getResponse()->getBody());
        }

        if (!$requestException) {
            $this->assertFalse($httpSuite->hasException());
            $this->assertEmpty($httpSuite->getException());

        } else {
            $this->assertTrue($httpSuite->hasException());
            $this->assertInstanceOf(HttpException::class, $httpSuite->getException());
            $this->assertInstanceOf(DateTime::class, $httpSuite->getException()->getCreated());

            foreach (HttpExceptionFactory::HANDLER_CONTEXT_PROPERTY_MAP as $handlerContextKey => $propertyName) {
                if (array_key_exists($handlerContextKey, $requestException->getHandlerContext())) {
                    $this->assertSame($requestException->getHandlerContext()[$handlerContextKey], $httpSuite->getException()->{'get'.ucfirst($propertyName)}());
                }
            }
        }
    }
}
