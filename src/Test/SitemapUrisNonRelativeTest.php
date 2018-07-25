<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\SitemapResult;
use Siteqa\Test\Domain\Model\Uri;

class SitemapUrisNonRelativeTest implements TestInterface
{
    const NAME = 'sitemap.uris_non_relative';

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
            $relativeUris = $this->sitemapResult->getUris()->filter(function (Uri $uri) {
                return trim($uri->getHost()) === '';
            });

            return new TestResult(self::NAME, $relativeUris->isEmpty() ? TestResult::STATUS_OK : TestResult::STATUS_ERROR, [
                'uri_count' => $this->sitemapResult->getUris()->count(),
                'relative' => $relativeUris->map(function (Uri $uri) {
                    return $uri->__toString();
                }),
            ]);
        }

        return TestResult::createError(self::NAME, [
            'uri_count' => $this->sitemapResult->getUris()->count(),
            'relative' => [],
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapUrisNonRelativeTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return implode('. ', array_filter([
                    sprintf('Found %d URIs', $testResult->getData()['uri_count']),
                    count($testResult->getData()['relative']) > 0 ? sprintf(
                        '%d are relative: %s',
                        count($testResult->getData()['relative']),
                        implode(', ', $testResult->getData()['relative'])
                    ) : null
                ]));
            }
        };
    }
}
