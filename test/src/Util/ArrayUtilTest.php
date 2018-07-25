<?php

declare(strict_types = 1);

namespace Siteqa\TestTest\Util;

use PHPUnit\Framework\TestCase;
use Siteqa\Test\Util\ArrayUtil;

class ArrayUtilTest extends TestCase
{
    /**
     * @dataProvider arrayGroupCountProvider
     *
     * @param array $input
     * @param array $expected
     */
    public function testArrayGroupCount(array $input, array $expected): void
    {
        $output = ArrayUtil::arrayGroupCount($input);
        $diff = array_diff_assoc($output, $expected);
        $this->assertCount(0, $diff, 'Expected no difference.');
    }

    public function arrayGroupCountProvider(): array
    {
        return [
            [
                ['test1', 'test1', 'test2', 'test3'],
                ['test1' => 2, 'test2' => 1, 'test3' => 1],
            ],
            [
                ['test1', 'test1', 'test2', 'test2'],
                ['test1' => 2, 'test2' => 2],
            ],
            [
                [],
                [],
            ],
            [
                [1, 2, 'test1', 'test2'],
                ['test1' => 1, 'test2' => 1],
            ]
        ];
    }
}
