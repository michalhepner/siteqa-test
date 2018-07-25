<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Test\HttpValidResponseCodeTest;
use Siteqa\Test\Test\TestResult;

class HttpValidResponseCodeTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(HttpValidResponseCodeTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new HttpValidResponseCodeTest($httpSuite);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', HttpValidResponseCodeTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                [200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['code' => 200]]
            ],
            [
                [500, 'Internal server error', ['errno' => 1234567, 'error' => 'abcd']],
                [TestResult::STATUS_ERROR, ['code' => 500]]
            ],
        ];
    }
}
