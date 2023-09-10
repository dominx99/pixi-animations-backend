<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

final readonly class ImageFrameSize
{
    public function __construct(
        public readonly int $columns,
        public readonly int $rows,
        public readonly int $framesX,
        public readonly int $framesY,
    ) {
    }
}
