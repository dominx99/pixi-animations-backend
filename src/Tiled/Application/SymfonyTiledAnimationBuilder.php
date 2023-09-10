<?php

declare(strict_types=1);

namespace App\Tiled\Application;

use App\Tiled\Domain\ValueObject\Animation;
use App\Tiled\Domain\ValueObject\Frame;
use App\Tiled\Domain\ValueObject\Tile;
use App\Tiled\Domain\ValueObject\Tileset;
use App\Tiles\Domain\ValueObject\AnimatedTile;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\SerializerInterface;

final class SymfonyTiledAnimationBuilder implements TiledAnimationBuilder
{
    public function __construct(SerializerInterface $serializer)
    {
    }

    /** @param Collection<AnimatedTile> $animatedTiles */
    public function build(Tileset $tileset, Collection $animatedTiles): Tileset
    {
        foreach ($animatedTiles as $animatedTile) {
            $animation = new Animation();

            if ($animatedTile->tiles->count() <= 1) {
                continue;
            }

            foreach ($animatedTile->tiles as $tile) {
                $animation->addFrame(new Frame(
                    $tile->id->value,
                    100,
                ));
            }

            if ($animation->getFrames()->count() <= 0) {
                continue;
            }

            $tileset->addTile(new Tile(
                $animation->getFrames()->first()->getTileId(),
                $animation
            ));
        }

        $tileset->reorder();

        return $tileset;
    }
}
