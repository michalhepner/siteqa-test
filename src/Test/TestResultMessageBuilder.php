<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Test;

use UnexpectedValueException;

class TestResultMessageBuilder implements TestResultMessageBuilderInterface
{
    /**
     * @var TestResultMessageBuilderInterface[]
     */
    protected $builders = [];

    public function __construct()
    {
        $defaultBuilders = [
            HttpHttpsRedirectTest::getMessageBuilder(),
            HttpNoDomainRedirectTest::getMessageBuilder(),
            HttpNoExceptionTest::getMessageBuilder(),
            HttpNoSslExceptionTest::getMessageBuilder(),
            HttpSameRedirectPathTest::getMessageBuilder(),
            HttpValidResponseCodeTest::getMessageBuilder(),
            HttpValidResponseTimeTest::getMessageBuilder(),
            SitemapNonEmptyTest::getMessageBuilder(),
            SitemapUrisLowercaseTest::getMessageBuilder(),
            SitemapUrisNonRelativeTest::getMessageBuilder(),
            SitemapUrisSameDomainTest::getMessageBuilder(),
            SitemapUrisSameSchemeTest::getMessageBuilder(),
            SitemapUrisUniqueTest::getMessageBuilder(),
        ];

        foreach ($defaultBuilders as $builder) {
            $this->add($builder);
        }
    }

    public function add(TestResultMessageBuilderInterface $builder): self
    {
        $this->builders[] = $builder;

        return $this;
    }

    public function canBuildMessage(TestResult $testResult): bool
    {
        foreach ($this->builders as $builder) {
            if ($builder->canBuildMessage($testResult)) {
                return true;
            }
        }

        return false;
    }

    public function buildMessage(TestResult $testResult): string
    {
        foreach ($this->builders as $builder) {
            if ($builder->canBuildMessage($testResult)) {
                return $builder->buildMessage($testResult);
            }
        }

        throw new UnexpectedValueException(sprintf(
            'No message builder available for test suite of test \'%s\' with status \'%s\'',
            $testResult->getTestName(),
            $testResult->getStatus()
        ));
    }
}
