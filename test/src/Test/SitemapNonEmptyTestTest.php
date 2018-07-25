<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Test\SitemapNonEmptyTest;
use Siteqa\App\Test\Test\TestResult;

class SitemapNonEmptyTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(SitemapNonEmptyTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapNonEmptyTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapNonEmptyTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['https://example.com/sitemap.xml', ['https://example.com'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['count' => 1]]
            ],
            [
                ['https://example.com/sitemap.xml', ['https://example.com', 'https://example.com/page1'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['count' => 2]]
            ],
            [
                ['https://example.com/sitemap.xml', [], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, ['count' => 0]]
            ],
            [
                ['https://example.com/sitemap.xml', [], 500, 'Internal server error', ['error' => 'Internal server error']],
                [TestResult::STATUS_ERROR, ['count' => 0]]
            ],
        ];
    }
}
