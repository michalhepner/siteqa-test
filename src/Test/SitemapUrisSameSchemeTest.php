<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Domain\Model\Uri;
use Siteqa\App\Test\Util\ArrayUtil;

class SitemapUrisSameSchemeTest implements TestInterface
{
    const NAME = 'sitemap.uris_same_scheme';

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
        $uriCount = $this->sitemapResult->getUris()->count();
        $requestedScheme = $this->sitemapResult->getSitemap()->getUri()->getScheme();
        $requestedSchemeCount = 0;
        $otherSchemesCount = [];
        $status = TestResult::STATUS_ERROR;

        if ($uriCount > 0) {
            $uriSchemes = array_filter($this->sitemapResult->getUris()->map(function (Uri $uri) {
                return $uri->getScheme() !== '' ? $uri->getScheme() : 'none';
            }));

            $uriSchemesCount = ArrayUtil::arrayGroupCount($uriSchemes);

            if (array_key_exists($requestedScheme, $uriSchemesCount)) {
                $requestedSchemeCount = $uriSchemesCount[$requestedScheme];
                unset($uriSchemesCount[$requestedScheme]);
            }

            $otherSchemesCount = $uriSchemesCount;

            if (count($otherSchemesCount) === 0) {
                $status = TestResult::STATUS_OK;
            }
        }

        return new TestResult(self::NAME, $status, [
            'uri_count' => $uriCount,
            'requested_scheme' => $requestedScheme,
            'requested_scheme_count' => $requestedSchemeCount,
            'other_schemes_count' => $otherSchemesCount,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapUrisSameSchemeTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                $data = $testResult->getData();

                return implode('. ', array_filter(array_merge(
                    [
                        sprintf('Found %d URIs', $data['uri_count']),
                        sprintf('%d URIs with scheme %s', $data['requested_scheme_count'], $data['requested_scheme']),
                    ],
                    array_map(
                        function ($scheme, $count) {
                            return sprintf('%d URIs with scheme %s', $count, $scheme);
                        },
                        array_keys($data['other_schemes_count']),
                        array_values($data['other_schemes_count'])
                    )
                )));
            }
        };
    }
}
