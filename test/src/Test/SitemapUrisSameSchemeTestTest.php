<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Domain\Model\SitemapResult;
use Siteqa\Test\Test\SitemapUrisSameSchemeTest;
use Siteqa\Test\Test\TestResult;

class SitemapUrisSameSchemeTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(SitemapUrisSameSchemeTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapUrisSameSchemeTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapUrisSameSchemeTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['http://example.com/sitemap.xml', ['http://example.com', 'http://example.com/page1'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, [
                    'uri_count' => 2,
                    'requested_scheme' => 'http',
                    'requested_scheme_count' => 2,
                    'other_schemes_count' => [],
                ]],
                ['https://example.com/sitemap.xml', ['https://example.com', 'http://example.com/page1', '/page2', 'ftp://something.com/thing2'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, [
                    'uri_count' => 4,
                    'requested_scheme' => 'https',
                    'requested_scheme_count' => 1,
                    'other_schemes_count' => [
                        'http' => 1,
                        'none' => 1,
                        'ftp' => 1,
                    ]
                ]],
            ],
        ];
    }
}
