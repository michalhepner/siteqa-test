<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Test\HttpValidResponseTimeTest;
use Siteqa\App\Test\Test\TestResult;

class HttpValidResponseTimeTestTest extends AbstractTestTest
{
    /**
     * @dataProvider provider
     *
     * @param int $errorResponseTime
     * @param int|null $warningResponseTime
     * @param array $httpSuiteConfiguration
     * @param array $testResultConfiguration
     */
    public function testExecute(int $errorResponseTime, ?int $warningResponseTime, array $httpSuiteConfiguration, array $testResultConfiguration): void
    {
        $httpSuite = call_user_func_array([$this, 'createHttpSuite'], $httpSuiteConfiguration);
        $expectedResult = new TestResult(HttpValidResponseTimeTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpValidResponseTimeTest($httpSuite, $errorResponseTime, $warningResponseTime);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpValidResponseTimeTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                150,
                50,
                [200, 'Lorem ipsum', null, null, 35],
                [TestResult::STATUS_OK, ['response_time' => 35, 'error_time' => 150, 'warning_time' => 50]]
            ],
            [
                150,
                50,
                [200, 'Lorem ipsum', null, null, 75],
                [TestResult::STATUS_WARNING, ['response_time' => 75, 'error_time' => 150, 'warning_time' => 50]]
            ],
            [
                150,
                50,
                [200, 'Lorem ipsum', null, null, 150],
                [TestResult::STATUS_ERROR, ['response_time' => 150, 'error_time' => 150, 'warning_time' => 50]]
            ],
        ];
    }
}
