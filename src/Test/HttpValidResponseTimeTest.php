<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\HttpSuite;

/**
 * Test checking if the response time matched the requirements.
 */
class HttpValidResponseTimeTest implements TestInterface
{
    const NAME = 'http.valid_response_time';

    /**
     * @var HttpSuite
     */
    protected $httpSuite;

    /**
     * @var int
     */
    protected $errorResponseTime;

    /**
     * @var int|null
     */
    protected $warningResponseTime;

    public function __construct(HttpSuite $httpSuite, int $errorResponseTime, ?int $warningResponseTime = null)
    {
        $this->httpSuite = $httpSuite;
        $this->errorResponseTime = $errorResponseTime;
        $this->warningResponseTime = $warningResponseTime;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function execute(): TestResult
    {
        $status = $this->httpSuite->getDuration() < $this->errorResponseTime ?
            ($this->warningResponseTime !== null && $this->httpSuite->getDuration() > $this->warningResponseTime ? TestResult::STATUS_WARNING : TestResult::STATUS_OK) :
            TestResult::STATUS_ERROR
        ;

        return new TestResult(self::NAME, $status, [
            'response_time' => $this->httpSuite->getDuration(),
            'error_time' => $this->errorResponseTime,
            'warning_time' => $this->warningResponseTime,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpValidResponseTimeTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return sprintf(
                    'Response time was %d ms, error threshold was set to %d ms, warning %s',
                    $testResult->getData()['response_time'],
                    $testResult->getData()['error_time'],
                    $testResult->getData()['warning_time'] !== null ? $testResult->getData()['warning_time'].' ms' : 'was not defined'
                );
            }
        };
    }
}
