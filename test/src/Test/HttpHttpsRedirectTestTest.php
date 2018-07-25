<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Domain\Collection\HttpRedirectCollection;
use Siteqa\Test\Domain\Model\HttpRedirect;
use Siteqa\Test\Domain\Model\HttpRequest;
use Siteqa\Test\Domain\Model\Uri;
use Siteqa\Test\Test\HttpHttpsRedirectTest;
use Siteqa\Test\Test\TestResult;

class HttpHttpsRedirectTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(HttpHttpsRedirectTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpHttpsRedirectTest($httpSuite);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpHttpsRedirectTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                [200, 'Lorem ipsum', null, 'http://example.com', null, new HttpRedirectCollection([
                    new HttpRedirect(
                        new HttpRequest(Uri::createFromString('http://example.com')),
                        new HttpRequest(Uri::createFromString('https://example.com'))
                    )
                ])],
                [TestResult::STATUS_OK, ['has_exception' => false, 'ending_scheme' => 'https']]
            ],
            [
                [200, 'Lorem ipsum', null, 'https://example.com', null, new HttpRedirectCollection([
                    new HttpRedirect(
                        new HttpRequest(Uri::createFromString('https://example.com')),
                        new HttpRequest(Uri::createFromString('http://example.com'))
                    )
                ])],
                [TestResult::STATUS_ERROR, ['has_exception' => false, 'ending_scheme' => 'http']]
            ],
            [
                [200, 'Lorem ipsum', null, 'https://example.com'],
                [TestResult::STATUS_OK, ['has_exception' => false, 'ending_scheme' => 'https']]
            ],
            [
                [200, 'Lorem ipsum', null, 'http://example.com'],
                [TestResult::STATUS_ERROR, ['has_exception' => false, 'ending_scheme' => 'http']]
            ],
            [
                [500, 'Lorem ipsum', ['error' => 'Internal server error'], 'https//example.com'],
                [TestResult::STATUS_ERROR, ['has_exception' => true, 'ending_scheme' => null]]
            ],
        ];
    }
}
