<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\Test\Domain\Model\HttpRedirect;
use Siteqa\Test\Domain\Model\HttpRequest;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Test\HttpNoDomainRedirectTest;
use Siteqa\Test\Test\TestResult;

class HttpNoDomainRedirectTestTest extends AbstractTestTest
{
    /**
     * @dataProvider provider
     *
     * @param array $httpSuiteConfiguration
     * @param array $testResultConfiguration
     */
    public function testExecute(array $httpSuiteConfiguration, array $testResultConfiguration): void
    {
        $httpSuite = call_user_func_array([$this, 'createHttpSuite'], $httpSuiteConfiguration);
        $expectedResult = new TestResult(HttpNoDomainRedirectTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpNoDomainRedirectTest($httpSuite);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpNoDomainRedirectTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                [200, 'Lorem ipsum', null, 'http://example.com', null, new HttpRedirectCollection([
                    new HttpRedirect(
                        new HttpRequest(Uri::createFromString('http://example.com')),
                        new HttpRequest(Uri::createFromString('https://something.com'))
                    )
                ])],
                [TestResult::STATUS_WARNING, ['last_url' => 'https://something.com', 'initial_host' => 'example.com', 'ending_host' => 'something.com']]
            ],
            [
                [200, 'Lorem ipsum', null, 'http://example.com', null, new HttpRedirectCollection([
                    new HttpRedirect(
                        new HttpRequest(Uri::createFromString('http://example.com')),
                        new HttpRequest(Uri::createFromString('https://www.example.com'))
                    )
                ])],
                [TestResult::STATUS_WARNING, ['last_url' => 'https://www.example.com', 'initial_host' => 'example.com', 'ending_host' => 'www.example.com']]
            ],
            [
                [200, 'Lorem ipsum', null, 'https://example.com', null, new HttpRedirectCollection([
                    new HttpRedirect(
                        new HttpRequest(Uri::createFromString('https://example.com')),
                        new HttpRequest(Uri::createFromString('http://example.com'))
                    )
                ])],
                [TestResult::STATUS_OK, ['last_url' => 'http://example.com', 'initial_host' => 'example.com', 'ending_host' => 'example.com']]
            ],
            [
                [200, 'Lorem ipsum', null, 'https://example.com'],
                [TestResult::STATUS_OK, ['last_url' => 'https://example.com', 'initial_host' => 'example.com', 'ending_host' => 'example.com']]
            ],
            [
                [500, 'Lorem ipsum', ['error' => 'Internal server error'], 'https://example.com'],
                [TestResult::STATUS_OK, ['last_url' => 'https://example.com', 'initial_host' => 'example.com', 'ending_host' => 'example.com']]
            ],
        ];
    }
}
