<?php declare(strict_types=1);

namespace Lexington\Common;

final class CSV
{
    public static function rows(string $file_name): array
    {
        $rows = [];
        $file = fopen($file_name, 'r');
        while ($row = fgetcsv($file)) {
            $rows[] = $row;
        }

        $header = array_shift($rows);
        $csv = [];

        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }

        return $csv;
    }
}
