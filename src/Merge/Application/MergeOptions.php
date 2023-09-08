<?php

declare(strict_types=1);

namespace App\Merge\Application;

final readonly class MergeOptions
{
    public function __construct(
        public int $imageWidth,
        public int $imageHeight,
        public int $tileWidth,
        public int $tileHeight,
        public int $framesX,
        public int $framesY,
    ) {
    }
}
