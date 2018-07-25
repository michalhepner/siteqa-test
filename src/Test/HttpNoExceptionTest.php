<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\HttpSuite;

class HttpNoExceptionTest implements TestInterface
{
    const NAME = 'http.no_exception';

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
        $exception = $this->httpSuite->getException();

        return new TestResult(
            self::NAME,
            $exception !== null ? TestResult::STATUS_ERROR : TestResult::STATUS_OK,
            [
                'exception' => $exception !== null ? $exception->getErrorMessage() : null,
            ]
        );
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpNoExceptionTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return $testResult->getData()['exception'] !== null ?
                    sprintf('Exception thrown: \'%s\'', $testResult->getData()['exception']) :
                    'No exceptions thrown'
                ;
            }
        };
    }
}
