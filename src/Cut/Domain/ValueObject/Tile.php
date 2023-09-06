<?php

declare(strict_types=1);

namespace App\Cut\Domain\ValueObject;

final readonly class Tile
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly string $path,
    ) {
    }

    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'path' => $this->path,
        ];
    }
}
