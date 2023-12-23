<?php

namespace App\Cut\Application;

use App\Cut\Domain\ValueObject\Tile;
use App\Tiles\Domain\ValueObject\AnimatedTile;
use Doctrine\Common\Collections\ArrayCollection;

final class AnimatedTiles extends ArrayCollection
{
    public function toArray(): array
    {
        return array_map(function (AnimatedTile $animatedTile) {
            return [
                'x' => $animatedTile->position->x,
                'y' => $animatedTile->position->y,
                'tiles' => array_map(fn (Tile $tile) => [
                    'x' => $tile->x,
                    'y' => $tile->y,
                    'path' => $tile->path,
                ], $animatedTile->tiles->toArray()),
            ];
        }, $this->getValues());
    }
}
