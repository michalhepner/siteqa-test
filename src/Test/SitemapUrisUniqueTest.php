<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Collection\UriCollection;
use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Domain\Model\Uri;

class SitemapUrisUniqueTest implements TestInterface
{
    const NAME = 'sitemap.uris_unique';

    /**
     * @var SitemapResult
     */
    protected $sitemapResult;

    /**
     * @var bool
     */
    protected $caseSensitive = false;

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
        $uriCount = $this->sitemapResult->getUris()->count();
        $duplicates = [];
        $status = TestResult::STATUS_ERROR;

        if ($uriCount > 0) {
            $duplicates = $this
                ->sitemapResult
                ->getUris()
                ->duplicated([!$this->caseSensitive ? UriCollection::CASE_INSENSITIVE : null])
                ->map(function (Uri $uri) {
                    return $uri->__toString();
                })
            ;

            if (count($duplicates) === 0) {
                $status = TestResult::STATUS_OK;
            }
        }

        return new TestResult(self::NAME, $status, [
            'uri_count' => $uriCount,
            'duplicates' => $duplicates,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapUrisUniqueTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                $data = $testResult->getData();

                return implode('. ', array_filter([
                    sprintf('Found %d URIs', $data['uri_count']),
                    sprintf('Found %d duplicates', count($data['duplicates']))
                ]));
            }
        };
    }

    public function getCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $caseSensitive): self
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }
}
