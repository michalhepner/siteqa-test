<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\HttpSuite;

/**
 * Test checking if response had HTTP 200 status code.
 */
class HttpValidResponseCodeTest implements TestInterface
{
    const NAME = 'http.valid_response_code';

    /**
     * @var HttpSuite
     */
    protected $httpSuite;

    public function __construct(HttpSuite $httpSuite)
    {
        $this->httpSuite = $httpSuite;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function execute(): TestResult
    {
        $httpSuite = $this->httpSuite;

        return new TestResult(
            self::NAME,
            $httpSuite->hasResponse() && $httpSuite->getResponse()->getStatusCode() === 200 ? TestResult::STATUS_OK : TestResult::STATUS_ERROR,
            ['code' => $httpSuite->hasResponse() ? $httpSuite->getResponse()->getStatusCode() : null]
        );
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpValidResponseCodeTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return $testResult->getData()['code'] !== null ?
                    sprintf('Response code was %d. Expected code 200', $testResult->getData()['code']) :
                    'Response code was not available'
                ;
            }
        };
    }
}
