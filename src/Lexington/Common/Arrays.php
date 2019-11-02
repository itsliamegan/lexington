<?php declare(strict_types=1);

namespace Lexington\Common;

final class Arrays
{
    public static function camelcase($array): array
    {
        $camelcase_array = [];

        foreach ($array as $key => $value) {
            $camelcase_key = $key;

            if (is_string($key)) {
                $camelcase_key = Strings::camelcase($key);
            }

            if (is_iterable($value)) {
                $value = Arrays::camelcase($value);
            }
            
            $camelcase_array[$camelcase_key] = $value;
        }

        return $camelcase_array;
    }
}
