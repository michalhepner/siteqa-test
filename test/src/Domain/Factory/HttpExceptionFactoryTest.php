<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Domain\Factory;

use PHPUnit\Framework\TestCase;
use Siteqa\App\Test\Domain\Factory\HttpExceptionFactory;

class HttpExceptionFactoryTest extends TestCase
{
    /**
     * @dataProvider createFromGuzzleHandlerContextProvider
     *
     * @param array $handlerContext
     */
    public function testCreateFromGuzzleHandlerContext(array $handlerContext): void
    {
        $exception = HttpExceptionFactory::createFromGuzzleHandlerContext($handlerContext);

        foreach (HttpExceptionFactory::HANDLER_CONTEXT_PROPERTY_MAP as $handlerContextKey => $propertyName) {
            if (isset($handlerContext[$handlerContextKey])) {
                $this->assertSame($handlerContext[$handlerContextKey], $exception->{'get'.ucfirst($propertyName)}());
            }
        }
    }

    public function createFromGuzzleHandlerContextProvider(): array
    {
        return [
            [[
                'errno' => 51,
                'error' => 'SSL: no alternative certificate subject name matches target host name \'artrox.se\'',
                'url' => 'https://artrox.se/sitemap.xml',
                'content_type' => NULL,
                'http_code' => 0,
                'header_size' => 0,
                'request_size' => 0,
                'filetime' => -1,
                'ssl_verify_result' => 1,
                'redirect_count' => 0,
                'total_time' => 0.275658,
                'namelookup_time' => 0.061225,
                'connect_time' => 0.127292,
                'pretransfer_time' => 0.0,
                'size_upload' => 0.0,
                'size_download' => 0.0,
                'speed_download' => 0.0,
                'speed_upload' => 0.0,
                'download_content_length' => -1.0,
                'upload_content_length' => -1.0,
                'starttransfer_time' => 0.0,
                'redirect_time' => 0.0,
                'redirect_url' => '',
                'primary_ip' => '79.125.106.190',
                'certinfo' => [],
                'primary_port' => 443,
                'local_ip' => '172.20.0.8',
                'local_port' => 53916,
            ]],
        ];
    }
}
