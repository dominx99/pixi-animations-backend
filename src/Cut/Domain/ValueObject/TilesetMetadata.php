<?php

declare(strict_types=1);

namespace App\Cut\Domain\ValueObject;

final class TilesetMetadata
{
    private array $tiles = [];
    private string $name;

    public function __construct(
        public readonly int $framesX,
        public readonly int $framesY,
        public readonly int $tileWidth,
        public readonly int $tileHeight,
    ) {
    }

    public function addTile(Tile $tile): void
    {
        $this->tiles[] = $tile;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'framesX' => $this->framesX,
            'framesY' => $this->framesY,
            'tileWidth' => $this->tileWidth,
            'tileHeight' => $this->tileHeight,
            'tiles' => array_map(fn (Tile $tile) => $tile->toArray(), $this->tiles),
        ];
    }
}
