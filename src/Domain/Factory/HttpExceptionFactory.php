<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Domain\Factory;

use Siteqa\App\Test\Domain\Model\HttpException;

class HttpExceptionFactory
{
    const HANDLER_CONTEXT_PROPERTY_MAP = [
        'errno' => 'errorNumber',
        'error' => 'errorMessage',
        'url' => 'url',
        'content_type' => 'contentType',
        'http_code' => 'httpCode',
        'header_size' => 'headerSize',
        'request_size' => 'requestSize',
        'filetime' => 'fileTime',
        'ssl_verify_result' => 'sslVerifyResult',
        'redirect_count' => 'redirectCount',
        'total_time' => 'totalTime',
        'namelookup_time' => 'nameLookupTime',
        'connect_time' => 'connectTime',
        'pretransfer_time' => 'preTransferTime',
        'size_upload' => 'sizeUpload',
        'size_download' => 'sizeDownload',
        'speed_download' => 'speedDownload',
        'speed_upload' => 'speedUpload',
        'download_content_length' => 'downloadContentLength',
        'upload_content_length' => 'uploadContentLength',
        'starttransfer_time' => 'startTransferTime',
        'redirect_time' => 'redirectTime',
        'certinfo' => 'certInfo',
        'request_header' => 'requestHeader',
    ];

    public static function createFromGuzzleHandlerContext(array $handlerContext): HttpException
    {
        $obj = new HttpException();
        foreach (self::HANDLER_CONTEXT_PROPERTY_MAP as $handlerContextKey => $propertyName) {
            if (array_key_exists($handlerContextKey, $handlerContext)) {
                $obj->{'set'.ucfirst($propertyName)}($handlerContext[$handlerContextKey]);
            }
        }

        return $obj;
    }
}
