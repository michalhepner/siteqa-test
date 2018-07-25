<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Test\SitemapUrisSameDomainTest;
use Siteqa\App\Test\Test\TestResult;

class SitemapUrisSameDomainTestTest extends AbstractTestTest
{
    /**
     * @dataProvider provider
     *
     * @param array $sitemapResultConfiguration
     * @param array $testResultConfiguration
     */
    public function testExecute(array $sitemapResultConfiguration, array $testResultConfiguration): void
    {
        /** @var SitemapResult $sitemapResult */
        $sitemapResult = call_user_func_array([$this, 'createSitemapResult'], $sitemapResultConfiguration);
        $expectedResult = new TestResult(SitemapUrisSameDomainTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapUrisSameDomainTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapUrisSameDomainTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['https://example.com/sitemap.xml', ['https://example.com'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, [
                    'requested_domain' => 'example.com',
                    'uri_count' => 1,
                    'same_domain_uri_count' => 1,
                    'similar_domain_uris' => [],
                    'relative_uris' => [],
                    'other_domain_uris' => [],
                ]],
            ],
            [
                ['https://example.com/sitemap.xml', ['https://example.com', 'https://www.example.com/page1', '/page2', 'https://other.com/page3'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, [
                    'requested_domain' => 'example.com',
                    'uri_count' => 4,
                    'same_domain_uri_count' => 1,
                    'similar_domain_uris' => [
                        'www.example.com' => ['https://www.example.com/page1'],
                    ],
                    'relative_uris' => ['/page2'],
                    'other_domain_uris' => [
                        'other.com' => ['https://other.com/page3'],
                    ],
                ]],
            ],
        ];
    }
}
