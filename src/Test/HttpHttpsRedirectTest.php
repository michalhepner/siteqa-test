<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Model\HttpRequest;
use Siteqa\App\Test\Domain\Model\HttpSuite;

/**
 * Test checking if the request ended on HTTPs scheme.
 */
class HttpHttpsRedirectTest implements TestInterface
{
    const NAME = 'http.https_redirect';

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
        if (!$this->httpSuite->hasException()) {
            $redirects = $this->httpSuite->getRedirects();
            $lastRequest = $redirects->isEmpty() ? $this->httpSuite->getRequest() : $redirects->last()->getTo();
            $status = $lastRequest->getUri()->getScheme() === HttpRequest::SCHEME_HTTPS ? TestResult::STATUS_OK : TestResult::STATUS_ERROR;

            return new TestResult(self::NAME, $status, [
                'has_exception' => false,
                'ending_scheme' => $lastRequest->getUri()->getScheme(),
            ]);
        }

        return TestResult::createError(self::NAME, ['has_exception' => true, 'ending_scheme' => null]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpHttpsRedirectTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                if ($testResult->getData()['has_exception']) {
                    return 'Unable to determine finishing protocol, exception thrown during request';
                }

                return sprintf('Request redirected to %s', $testResult->getData()['ending_scheme']);
            }
        };
    }
}
