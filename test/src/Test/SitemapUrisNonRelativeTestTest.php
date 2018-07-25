<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Test;

use Siteqa\Test\Domain\Model\SitemapResult;
use Siteqa\Test\Test\SitemapUrisNonRelativeTest;
use Siteqa\Test\Test\TestResult;

class SitemapUrisNonRelativeTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(SitemapUrisNonRelativeTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapUrisNonRelativeTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapUrisNonRelativeTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['https://example.com/sitemap.xml', ['https://example.com'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['uri_count' => 1, 'relative' => []]],
            ],
            [
                ['https://example.com/sitemap.xml', [], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, ['uri_count' => 0, 'relative' => []]],
            ],
            [
                ['https://example.com/sitemap.xml', ['/', '/page1'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, ['uri_count' => 2, 'relative' => ['/', '/page1']]],
            ],
        ];
    }
}
