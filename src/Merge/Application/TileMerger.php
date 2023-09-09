<?php

declare(strict_types=1);

namespace App\Merge\Application;

use Imagick;

interface TileMerger
{
    public function merge(array $animationTiles, MergeOptions $options, string $outputFilename): Imagick;
    public function mergeStaticTileset(array $tiles, MergeOptions $options, string $outputFilename): Imagick;
}
