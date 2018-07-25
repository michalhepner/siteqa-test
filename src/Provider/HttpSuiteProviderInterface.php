<?php

declare(strict_types = 1);

namespace Siteqa\Test\Provider;

use GuzzleHttp\Promise\PromiseInterface;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Uri;

interface HttpSuiteProviderInterface
{
    public function provideSync(Uri $uri): HttpSuite;
    public function provideAsync(Uri $uri, callable $onFinish): PromiseInterface;
}
