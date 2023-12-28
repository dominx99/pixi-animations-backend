<?php

declare(strict_types=1);

namespace App\Cut\Application;

use Throwable;

use Doctrine\Common\Collections\ArrayCollection;
use App\Tiles\Domain\ValueObject\Position;
use App\Tiles\Domain\ValueObject\AnimatedTile;
use App\Tiles\Domain\ValueObject\Tiles;

final class TiledAnimationsTransformer
{
    public function transform(string $id, array $tiles, VerticalToHorizontalOptions $options): array
    {
        $tiles = Tiles::fromArray($tiles['tiles'], $id);
        $iterations = floor($tiles->rows() / $options->framesX);

        $animatedTiles = new AnimatedTiles();

        for ($x = 0; $x < $options->framesX; $x++) {
            for ($y = 0; $y < $options->framesY; $y++) {
                $animatedTile = new AnimatedTile(new Position($x, $y), new ArrayCollection());

                for ($i = 0; $i < $iterations; $i++) {
                    try {
                        $animatedTile->addTile($tiles->getTile(
                            new Position($x + $i * $options->framesX, $y)
                        ));
                    } catch (Throwable $e) {
                        $test = $e;
                    }
                }

                $animatedTiles->add($animatedTile);
            }
        }

        return $animatedTiles->toArray();
    }
}
