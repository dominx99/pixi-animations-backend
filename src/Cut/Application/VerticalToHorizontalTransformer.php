<?php

declare(strict_types=1);

namespace App\Cut\Application;

use App\Tiles\Application\ImagickImageSizeDeterminant;
use App\Tiles\Domain\ValueObject\AnimatedTile;
use App\Tiles\Domain\ValueObject\ImageFrameSize;
use App\Tiles\Domain\ValueObject\Position;
use App\Tiles\Domain\ValueObject\Tiles;
use Doctrine\Common\Collections\ArrayCollection;
use Throwable;

final class VerticalToHorizontalTransformer
{
    public function __construct(private readonly ImagickImageSizeDeterminant $imagickImageSizeDeterminant)
    {
    }

    /**
     * @param array $tiles
     */
    public function transform(string $id, array $tiles, VerticalToHorizontalOptions $options): array
    {
        $tiles = Tiles::fromArray($tiles['tiles'], $id);
        $iterations = floor($tiles->columns() / $options->framesX);

        $animatedTiles = new AnimatedTiles();

        for ($x = 0; $x < $options->framesX; $x++) {
            for ($y = 0; $y < $options->framesY; $y++) {
                $animatedTile = new AnimatedTile(new Position($x, $y), new ArrayCollection());

                for ($i = 0; $i < $iterations; $i++) {
                    try {
                        $animatedTile->addTile($tiles->getTile(
                            new Position($x, $y + $i * $options->framesY)
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
