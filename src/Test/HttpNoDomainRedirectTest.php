<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Model\HttpSuite;

/**
 * Test checking if the request does not get redirected to a different domain than requested.
 */
class HttpNoDomainRedirectTest implements TestInterface
{
    const NAME = 'http.no_domain_redirect';

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
        $lastUrl = null;
        $endingHost = null;
        $initialHost = $this->httpSuite->getUri()->getHost();

        if ($this->httpSuite->hasResponse()) {
            if (!$this->httpSuite->getRedirects()->isEmpty()) {
                $lastRedirect = $this->httpSuite->getRedirects()->last()->getTo();
                $lastUrl = $lastRedirect->getUri();
                $endingHost = $lastUrl->getHost();
            } else {
                $lastUrl = $this->httpSuite->getRequest()->getUri();
                $endingHost = $lastUrl->getHost();
            }

            $status = $endingHost === $initialHost ? TestResult::STATUS_OK : TestResult::STATUS_WARNING;
        } else {
            $status = TestResult::STATUS_ERROR;
        }

        return new TestResult(self::NAME, $status, [
            'last_url' => $lastUrl ? $lastUrl->__toString() : null,
            'initial_host' => $initialHost,
            'ending_host' => $endingHost,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpNoDomainRedirectTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                $data = $testResult->getData();
                if (!empty($data['ending_host'])) {
                    return sprintf('Request finished on domain %s', $data['ending_host']);
                }

                return 'Unable to determine finishing domain, response was not present';
            }
        };
    }
}
