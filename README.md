# SiteQA Tests

A library providing site quality tests.

## Getting Started

### Prerequisites

The library depends on:
* **composer**
* **php**: >= 7.2
* **league/uri**: ^5.3
* **guzzlehttp/guzzle**: ^6.3
* **psr/log**: ^1.0
* **psr/http-message**: ^1.0
* **symfony/dom-crawler**: ~3.4|~4.0
* **symfony/css-selector**: ~3.4|~4.0
* **symfony/event-dispatcher**: ~3.4|~4.0 (OPTIONAL, to use built in event system)

### Installing

The project is not meant to be publicly available, therefore please add the repository URL to 
your **composer.json** file:

```
{
    (...)
    "repositories": [
        { "type": "vcs", "url": "https://example.com/siteqa/test" }
    ]
    (...)
}
```
**IMPORTANT**:
Please replace "https://example.com/siteqa/test" with the real repository URL.

Then run:

```
composer require siteqa/test
```

## Running the tests

Tests can be executed by running:

```
vendor/bin/phpunit tests
```

## Usage

The library is divided into two main categories of classes:

#### Providers

Used to acquire data for further analysis. The following providers are available:

###### HttpSuiteProvider

Allows to perform HTTP(s) requests to specified URLs and returns information about the response, redirects and the request itself.

The provider can be used in synchronous and asynchronous mode.

```
<?php

// autoload goes here...

use GuzzleHttp\Client as Guzzle;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\HttpSuiteProvider;

$guzzle = new Guzzle();
$provider = new HttpSuiteProvider($guzzle);
$provider
    ->setTimeout(120)
    ->setMaxRedirects(20)
;

$uri = Uri::createFromString('https://example.com');
$httpSuite = $provider->provideSync($uri);

// Do something with the result
```

```
<?php

// autoload goes here...

use GuzzleHttp\Client as Guzzle;
use Siteqa\Test\Domain\Model\HttpSuite;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\HttpSuiteProvider;

$guzzle = new Guzzle();
$provider = new HttpSuiteProvider($guzzle);
$provider
    ->setTimeout(15)
    ->setMaxRedirects(5)
;

$uri = Uri::createFromString('https://example.com');
$promise = $provider->provideAsync($uri, function (HttpSuite $httpSuite) {
    // Do seomething with the result
});
$promise->wait();
```

Allowed provider methods:

* setTimeout(int $timeout): self - time in seconds after which the request is aborted.
* setMaxRedirects(int $redirects): self - max number of redirects after which the request is aborted.
* provideSync(Siteqa\Test\Domain\Model\Uri $uri): Siteqa\Test\Domain\Model\HttpSuite - performs a synchronous HTTP call and returns gathered information
* provideAsync(Siteqa\Test\Domain\Model\Uri $uri, callable $onFinish): GuzzleHttp\Promise\PromiseInterface - performs a asynchronous HTTP call and returns a promise.

###### CrawlerResultProvider

Allows to crawl websites and return information about all the performed HTTP calls.

```
<?php

// autoload goes here...

use GuzzleHttp\Client as Guzzle;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Provider\CrawlerResultProvider;
use Siteqa\Test\Provider\HttpSuiteProvider;

$guzzle = new Guzzle();
$httpSuiteProvider = new HttpSuiteProvider($guzzle);

$crawlerResultProvider = new CrawlerResultProvider($httpSuiteProvider);

// OPTIONAL: ignore some of the URIs that were provided or found inside
//           HTTP responses
$crawlerResultProvider->addUriFilter(function (Uri $uri) {
    return $uri->__toString() !== 'https://example.com/some-subpage';
});

$initialUris = [
    'https://example.com',
    'https://example.com/some-subpage',
];
$allowedHosts = ['example.com', 'www.example.com'];
$crawlerResult = $crawlerResultProvider->provide($initialUris, $allowedHosts);

foreach ($crawlerResult as $httpSuite) {
    // Do something with each item
}
```

Allowed providerMethods:

* setPoolSize(int $poolSize): self - defines the number of concurrent HTTP that could be executed
* setRecursive(bool $recursive): self - defines if the crawler should include all found all links in the HTML source
and call them too.
* setIgnoreScheme(bool $ignoreScheme): self - used when recursive crawling is enabled. To avoid calling the same URIs
more than once, the crawler will check if a URI was already stored in it's memory. This flag will allow to ignore scheme
differences when checking if the URI was already called. For example, when dealing with 2 URIs:
https://example.com and http://example.com, having the flag enabled, will cause to call only the first URI, having the
flag disabled wll cause to call both.
* setIgnoreFragment(bool $ignoreFragment): self - works similar to setIgnoreScheme but ignores the fragment part
of the URI when checking if URI was already called.
* setCaseSensitive(bool $caseSensitive): self - works similar to setIgnoreScheme, if set, will not call 2 similar URIs
twice, if they differed only with the case.
* addUriFilter(callable $filter): self - allows to ignore URIs found by the Crawler by providing a callback function. If
`false` is returned from the callback function, the URI will be filtered out and not called by the Crawler.


###### SitemapResultProvider

Allows to scrape URIs stored inside sitemap.xml.

```
<?php

// autoload goes here...

use GuzzleHttp\Client as Guzzle;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Domain\Model\Sitemap;
use Siteqa\Test\Provider\SitemapResultProvider;
use Siteqa\Test\Provider\HttpSuiteProvider;

$guzzle = new Guzzle();
$httpSuiteProvider = new HttpSuiteProvider($guzzle);

$sitemapResultProvider = new SitemapResultProvider($httpSuiteProvider);
$sitemap = new Sitemap(Uri::createFromString('https://example.com/sitemap.xml'));

$sitemapResult = $sitemapResultProvider->provide($sitemap);

/** @var Uri $uri */
foreach ($sitemapResult->getUris() as $uri) {
    // Do something with each item
}
```

#### Tests

Used to check if data returned by providers adheres to the requirements.
Each `Test` returns a `TestResult`. A `TestResult` can have one of the following statuses:
* ok
* suspicious
* warning
* error
* critical

###### HttpHttpsRedirectTest

Checks if HTTP call was redirected to HTTPs scheme.

```
<?php

// (..) prepare the HttpSuiteProvider

use Siteqa\Test\Test\HttpHttpsRedirectTest;

$httpSuite = $httpSuiteProvider->provideSync($uri);
$test = new HttpHttpsRedirectTest($httpSuite);
$testResult = $test->execute();

echo $testResult->getStatus() // Outputs a status string like 'ok', 'error'...
echo HttpHttpsRedirectTest::getMessageBuilder()->buildMessage($testResult); // Outputs human readable info.
```

###### HttpNoDomainRedirectTest

Checks if the HTTP response comes from the same domain as the request was made?
Usage is similar to HttpHttpsRedirectTest.

###### HttpNoExceptionTest

Checks if there were any Exceptions thrown during the HTTP request.
Usage is similar to HttpHttpsRedirectTest.

###### HttpNoSslExceptionTest

Checks if there were any exceptions thrown during the HTTP request that are related to SSL.
Usage is similar to HttpHttpsRedirectTest.

###### HttpSameRedirectPathTest

Checks if the response URI path is the same as the request URI path.
Usage is similar to HttpHttpsRedirectTest.

###### HttpValidResponseCodeTest

Checks if the response code was HTTP 200.
Usage is similar to HttpHttpsRedirectTest.

###### HttpValidResponseTimeTest

Checks if the response came within an acceptable time period.

```
<?php

// (..) prepare the HttpSuiteProvider

use Siteqa\Test\Test\HttpValidResponseTimeTest;

$httpSuite = $httpSuiteProvider->provideSync($uri);
$errorTime = 1500; // 1500 milliseconds
$warningTime = 1000; // 1000 milliseconds
$test = new HttpValidResponseTimeTest($httpSuite, $errorTime, $warningTime);
$testResult = $test->execute();

echo $testResult->getStatus() // Outputs a status string like 'ok', 'warning', error'...
echo HttpValidResponseTimeTest::getMessageBuilder()->buildMessage($testResult); // Outputs human readable info.
```

###### SitemapNonEmptyTest

Checks if the provided sitemap holds any URIs.

```
<?php

// (..) prepare the SitemapResultProvider and sitemap

use Siteqa\Test\Test\SitemapNonEmptyTest;

$sitemapResult = $sitemapResultProvider->provide($sitemap);
$test = new SitemapNonEmptyTest($sitemapResult);
$testResult = $test->execute();

echo $testResult->getStatus() // Outputs a status string like 'ok', 'warning', error'...
echo SitemapNonEmptyTest::getMessageBuilder()->buildMessage($testResult); // Outputs human readable info.
```

###### SitemapUrisLowercaseTest

Checks if all the URIs available in the sitemap are lowercase.
Usage is similar to SitemapUrisLowercaseTest.

###### SitemapUrisNonRelativeTest

Checks if all the URIs available in the sitemap are absolute URIs.
Usage is similar to SitemapUrisLowercaseTest.

###### SitemapUrisSameDomainTest

Checks if all the URIs available in the sitemap point to the same domain from which te sitemap was requested.
Usage is similar to SitemapUrisLowercaseTest.

###### SitemapUrisSameSchemeTest

Checks if all the URIs available in the sitemap use the same scheme that was used to request the sitemap.
Usage is similar to SitemapUrisLowercaseTest.

###### SitemapUrisUniqueTest

Checks if all the URIs available in the sitemap are unique.
Usage is similar to SitemapUrisLowercaseTest.

#### For more tests and providers please check the source code.

## Authors

* **Michał Hepner** <michal.hepner@gmail.com> - *design and implementation*

## License

Copyright (C) Michał Hepner - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Michał Hepner <michal.hepner@gmail.com>, July 2018
