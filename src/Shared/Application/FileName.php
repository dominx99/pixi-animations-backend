<?php

declare(strict_types=1);

namespace App\Shared\Application;

final class FileName
{
    public static function fromString(string $filename): string
    {
        return str_replace(
            ' ',
            '-',
            trim(
                preg_replace(
                    '/\s+/',
                    ' ',
                    preg_replace('/\s+-\s+/', '-', $filename)
                )
            )
        );
    }
}
