<?php

declare(strict_types=1);

namespace App\Files\Application;

final class InternalJsonFileManager implements JsonFileManager
{
    public function save(string $path, array $data): void
    {
        file_put_contents($path, json_encode($data));
    }

    public function load(string $path): array
    {
        return json_decode(file_get_contents($path), true);
    }
}
