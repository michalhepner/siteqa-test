<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Test\HttpNoSslExceptionTest;
use Siteqa\Test\Test\TestResult;

class HttpNoSslExceptionTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(HttpNoSslExceptionTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpNoSslExceptionTest($httpSuite);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpNoSslExceptionTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                [200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['exception' => null, 'is_ssl_exception' => false]]
            ],
            [
                [null, null, ['errno' => 1234567, 'error' => 'abcd']],
                [TestResult::STATUS_ERROR, ['exception' => 'abcd', 'is_ssl_exception' => false]]
            ],
            [
                [null, null, ['errno' => 51, 'error' => 'Certificate wrong']],
                [TestResult::STATUS_ERROR, ['exception' => 'Certificate wrong', 'is_ssl_exception' => true]]
            ],
        ];
    }
}
