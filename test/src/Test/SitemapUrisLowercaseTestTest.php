<?php

declare(strict_types = 1);

namespace Siteqa\AppTest\Test\Test;

use Siteqa\App\Test\Domain\Model\SitemapResult;
use Siteqa\App\Test\Test\SitemapUrisLowercaseTest;
use Siteqa\App\Test\Test\TestResult;

class SitemapUrisLowercaseTestTest extends AbstractTestTest
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
        $expectedResult = new TestResult(SitemapUrisLowercaseTest::NAME, $testResultConfiguration[0], $testResultConfiguration[1]);

        $test = new SitemapUrisLowercaseTest($sitemapResult);
        $testResult = $test->execute();

        $this->assertSame($expectedResult->getTestName(), $testResult->getTestName());
        $this->assertSame($expectedResult->getStatus(), $testResult->getStatus());
        $this->assertEquals($expectedResult->getData(), $testResult->getData());
        $this->assertInternalType('string', SitemapUrisLowercaseTest::getMessageBuilder()->buildMessage($testResult));
    }

    public function provider()
    {
        return [
            [
                ['https://example.com/sitemap.xml', ['https://example.com'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_OK, ['uri_count' => 1, 'uppercase' => []]],
            ],
            [
                ['https://example.com/sitemap.xml', [], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, ['uri_count' => 0, 'uppercase' => []]],
            ],
            [
                ['https://example.com/sitemap.xml', ['https://example.com/page1', 'https://example.com/Page1'], 200, 'Lorem ipsum', null],
                [TestResult::STATUS_ERROR, ['uri_count' => 2, 'uppercase' => ['https://example.com/Page1']]],
            ],
        ];
    }
}
