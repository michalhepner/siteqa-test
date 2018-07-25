<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Util;

class ArrayUtil
{
    public static function arrayGroupCount(array $arr): array
    {
        $ret = [];
        foreach ($arr as $item) {
            if (is_string($item)) {
                !array_key_exists($item, $ret) && $ret[$item] = 0;
                $ret[$item]++;
            }
        }

        return $ret;
    }
}
