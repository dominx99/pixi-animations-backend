<?php

declare(strict_types=1);

namespace App\Merge\Application;

use App\Merge\Domain\ValueObject\ImageSize;

interface ImageSizeDeterminant
{
    public function determine(array $animationTiles, ImageSizeOptions $options): ImageSize;
    public function determineStaticTileset(array $tiles, ImageSizeOptions $options): ImageSize;
}
