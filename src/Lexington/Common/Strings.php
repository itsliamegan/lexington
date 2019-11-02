<?php declare(strict_types=1);

namespace Lexington\Common;

final class Strings
{
    public static function camelcase(string $str): string
    {
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $str) ?? $str;
        $str = trim($str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        $str = lcfirst($str);

        return $str;
    }
}
