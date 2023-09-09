<?php

declare(strict_types=1);

namespace App\Merge\Application;

use Imagick;

interface TilesetMerger
{
    public function merge(array $tilesets, MergeTilesetOptions $options, string $outputPath): Imagick;
}
