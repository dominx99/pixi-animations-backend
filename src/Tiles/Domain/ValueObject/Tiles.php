<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

use App\Cut\Domain\ValueObject\Tile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Tiles
{
    /** @param Collection<int, Tile> $tiles */
    public function __construct(private ArrayCollection $tiles)
    {
    }

    public static function fromArray(array $tiles, string $id): Tiles
    {
        $tiles = array_map(
            fn (array $tile) => new Tile($tile['x'], $tile['y'], sprintf('tilesets/%s/%s', $id, $tile['path'])),
            $tiles
        );

        return new self(new ArrayCollection($tiles));
    }

    public function columns(): int
    {
        $maxY = 0;

        /** @var Tile $tile */
        foreach ($this->tiles as $tile) {
            if ($tile->y > $maxY) {
                $maxY = $tile->y;
            }
        }

        return $maxY + 1;
    }

    public function getTile(Position $position): Tile
    {
        /** @var Tile $tile */
        foreach ($this->tiles as $tile) {
            if ($tile->x === $position->x && $tile->y === $position->y) {
                return $tile;
            }
        }

        throw new \InvalidArgumentException('Tile not found');
    }
}
