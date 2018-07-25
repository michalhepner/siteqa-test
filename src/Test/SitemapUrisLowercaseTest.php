<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\SitemapResult;
use Siteqa\Test\Domain\Model\Uri;

class SitemapUrisLowercaseTest implements TestInterface
{
    const NAME = 'sitemap.uris_lowercase';

    /**
     * @var SitemapResult
     */
    protected $sitemapResult;

    public function __construct(SitemapResult $sitemapResult)
    {
        $this->sitemapResult = $sitemapResult;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function execute(): TestResult
    {
        if (!$this->sitemapResult->getUris()->isEmpty()) {
            $uppercaseUris = $this->sitemapResult->getUris()->filter(function (Uri $uri) {
                return rawurldecode($uri->__toString()) !== strtolower(rawurldecode($uri->__toString()));
            });

            return new TestResult(self::NAME, $uppercaseUris->isEmpty() ? TestResult::STATUS_OK : TestResult::STATUS_ERROR, [
                'uri_count' => $this->sitemapResult->getUris()->count(),
                'uppercase' => $uppercaseUris->map(function (Uri $uri) {
                    return $uri->__toString();
                }),
            ]);
        }

        return TestResult::createError(self::NAME, [
            'uri_count' => $this->sitemapResult->getUris()->count(),
            'uppercase' => [],
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapUrisLowercaseTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return implode('. ', array_filter([
                    sprintf('Found %d URIs', $testResult->getData()['uri_count']),
                    count($testResult->getData()['uppercase']) > 0 ? sprintf(
                        '%d contain uppercase letters: %s',
                        count($testResult->getData()['uppercase']),
                        implode(', ', $testResult->getData()['uppercase'])
                    ) : null
                ]));
            }
        };
    }
}
