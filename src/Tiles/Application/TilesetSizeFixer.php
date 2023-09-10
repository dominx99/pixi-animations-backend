<?php

declare(strict_types=1);

namespace App\Tiles\Application;

interface TilesetSizeFixer
{
    public function fix(string $inputPath, int $tileWidth, int $tileHeight, callable $logger): string;
}
