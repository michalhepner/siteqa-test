<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

interface TestResultMessageBuilderInterface
{
    public function canBuildMessage(TestResult $testResult): bool;
    public function buildMessage(TestResult $testResult): string;
}
