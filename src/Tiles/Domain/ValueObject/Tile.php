<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

final readonly class Tile
{
    public function __construct(
        public TileId $id,
        public Position $position,
        public string $path,
    ) {
    }
}
