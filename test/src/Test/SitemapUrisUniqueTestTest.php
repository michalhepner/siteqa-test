<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Test\SitemapUrisUniqueTest;
use Siteqa\App\Test\Test\TestResult;

class SitemapUrisUniqueSchemeTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(SitemapUrisUniqueTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapUrisUniqueTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapUrisUniqueTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['https://example.com/sitemap.xml', ['https://example.com', 'http://example.com', '/page2'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, [
                    'uri_count' => 3,
                    'duplicates' => [],
                ]],
                ['https://example.com/sitemap.xml', ['https://example.com', 'http://example.com', '/page2', 'http://example.com', '/page2', '/page1'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, [
                    'uri_count' => 6,
                    'duplicates' => [
                        'http://example.com',
                        '/page2',
                    ],
                ]],
            ],
        ];
    }
}
