<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;

/**
 * Test checking if the sitemap is not empty.
 */
class SitemapNonEmptyTest implements TestInterface
{
    const NAME = 'sitemap.non_empty';

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
        return new TestResult(self::NAME, $this->sitemapResult->getUris()->isEmpty() ? TestResult::STATUS_ERROR : TestResult::STATUS_OK, [
            'count' => $this->sitemapResult->getUris()->count()
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapNonEmptyTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return sprintf('Sitemap contained %d URIs', $testResult->getData()['count']);
            }
        };
    }
}
