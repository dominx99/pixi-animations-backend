<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

final class TileId
{
    private function __construct(
        public readonly int $value,
    ) {
    }

    public static function fromPosition(Position $position, ImageFrameSize $imageFrameSize, int $iteration): self
    {
        /*
            iteration: 2
            position: 0, 0
            imageFrameSize: 5, 5

            expected tileid = 10

            tileid = 0 * 5 + 0 = 0
            tileid = 0 * 5 + 5 * 2 = 0

            tileid = 0 * 80 + (2 * 5) + 0 = 10

            iteration: 1
            position: 0, 0
            imageFrameSize: 80, 20, 5, 5
            expected tileid = 0

            tileid = 0 * 80 + (0 * 5) + 0 = 0
            tileid = 0 * 80 + (1 * 5) + 0 = 5
            tileid = 0 * 80 + (1 * 5) + 0 = 5
        */
        return new self(
            $position->y * $imageFrameSize->columns + ($iteration * $imageFrameSize->framesX) + $position->x,
        );
    }
}
