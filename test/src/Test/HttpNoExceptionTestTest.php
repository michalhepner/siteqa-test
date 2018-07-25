<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Test\HttpNoExceptionTest;
use Siteqa\App\Test\Test\TestResult;

class HttpNoExceptionTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(HttpNoExceptionTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpNoExceptionTest($httpSuite);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpNoExceptionTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                [200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['exception' => null]]
            ],
            [
                [null, null, ['errno' => 123, 'error' => 'abcd']],
                [TestResult::STATUS_ERROR, ['exception' => 'abcd']]
            ],
            [
                [500, 'Internal server error', ['errno' => 123, 'error' => 'abcd']],
                [TestResult::STATUS_ERROR, ['exception' => 'abcd']]
            ],
        ];
    }
}
