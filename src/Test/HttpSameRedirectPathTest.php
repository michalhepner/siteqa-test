<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\HttpSuite;

class HttpSameRedirectPathTest implements TestInterface
{
    const NAME = 'http.same_redirect_path';

    /**
     * @var HttpSuite
     */
    protected $httpSuite;

    /**
     * Should a warning be returned when paths differ?
     *
     * @var bool
     */
    protected $strict = false;

    public function __construct(HttpSuite $httpSuite, bool $strict = false)
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

        $initialPath = $httpSuite->getRequest()->getUri()->getPath();
        $endingPath = $httpSuite->hasRedirects() ? $httpSuite->getRedirects()->last()->getTo()->getUri()->getPath() : $initialPath;
        $status = $initialPath === $endingPath || !$this->strict ? TestResult::STATUS_OK : TestResult::STATUS_WARNING;

        return new TestResult(self::NAME, $status, [
            'initial_path' => $initialPath,
            'ending_path' => $endingPath,
            'redirect_count' => $httpSuite->getRedirects()->count(),
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpSameRedirectPathTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return $testResult->getData()['initial_path'] !== $testResult->getData()['ending_path'] ?
                    sprintf('A redirect happened from %s to %s', $testResult->getData()['initial_path'], $testResult->getData()['ending_path']) :
                    sprintf('Path stayed %s', $testResult->getData()['ending_path'])
                ;
            }
        };
    }
}
