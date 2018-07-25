<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Domain\Model\Uri;
use Siteqa\App\Test\Util\ArrayUtil;

class SitemapUrisSameDomainTest implements TestInterface
{
    const NAME = 'sitemap.uris_same_domain';

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
        $requestedHost = $this->sitemapResult->getSitemap()->getUri()->getHost();
        $sameDomainUriCount = 0;
        $similarDomainUris = [];
        $otherDomainUris = [];
        $relativeUris = [];
        $status = TestResult::STATUS_ERROR;

        if ($uriCount > 0) {
            $uriDomains = [];

            /** @var Uri $uri */
            foreach ($this->sitemapResult->getUris() as $uri) {
                $uriHost = trim((string) $uri->getHost());
                if ($uriHost !== '') {
                    !array_key_exists($uriHost, $uriDomains) && $uriDomains[$uriHost] = [];
                    $uriDomains[$uriHost][] = $uri;
                } else {
                    $relativeUris[] = $uri->__toString();
                }
            }

            $strippedRequestedHost = preg_replace('/^www\./', '', $requestedHost);

            foreach ($uriDomains as $uriHost => $uris) {
                if ($uriHost === $requestedHost) {
                    $sameDomainUriCount = count($uris);
                } elseif (preg_replace('/^www\./', '', $uriHost) === $strippedRequestedHost) {
                    $similarDomainUris[$uriHost] = $uris;
                } else {
                    $otherDomainUris[$uriHost] = $uris;
                }
            }

            if (count($otherDomainUris) > 0 || count($relativeUris) > 0) {
                $status = TestResult::STATUS_ERROR;
            } elseif(count($similarDomainUris) > 0) {
                $status = TestResult::STATUS_SUSPICIOUS;
            } else {
                $status = TestResult::STATUS_OK;
            }
        }

        return new TestResult(self::NAME, $status, [
            'requested_domain' => $requestedHost,
            'uri_count' => $this->sitemapResult->getUris()->count(),
            'same_domain_uri_count' => $sameDomainUriCount,
            'similar_domain_uris' => $similarDomainUris,
            'relative_uris' => $relativeUris,
            'other_domain_uris' => $otherDomainUris,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === SitemapUrisSameDomainTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                return implode('. ', array_filter(array_merge(
                    [
                        sprintf('Found %d URIs', $testResult->getData()['uri_count']),
                        sprintf('%d URIs found from domain %s', $testResult->getData()['same_domain_uri_count'], $testResult->getData()['requested_domain']),
                        count($testResult->getData()['relative_uris']) ? sprintf('%d URIs relative', count($testResult->getData()['relative_uris'])) : null,
                    ],
                    array_map(
                        function ($domain, $value) {
                            return sprintf('%d URIs found from domain %s', count($value), $domain);
                        },
                        array_keys($testResult->getData()['similar_domain_uris']),
                        array_values($testResult->getData()['similar_domain_uris'])
                    ),
                    array_map(
                        function ($domain, $value) {
                            return sprintf('%d URIs found from domain %s', count($value), $domain);
                        },
                        array_keys($testResult->getData()['other_domain_uris']),
                        array_values($testResult->getData()['other_domain_uris'])
                    )
                )));
            }
        };
    }
}
