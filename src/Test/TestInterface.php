<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

interface TestInterface
{
    public function getName(): string;
    public function execute(): TestResult;
}
