<?php

declare(strict_types=1);

namespace App\Files\Application;

interface JsonFileManager
{
    public function save(string $path, array $data): void;
    public function load(string $path): array;
}
