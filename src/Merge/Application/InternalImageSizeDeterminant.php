<?php

declare(strict_types=1);

namespace App\Merge\Application;

use App\Merge\Domain\ValueObject\ImageSize;

final class InternalImageSizeDeterminant implements ImageSizeDeterminant
{
    public function determine(array $animationTiles, ImageSizeOptions $options): ImageSize
    {
        $maxY = 1;
        $maxX = 1;
        $tilesInRow = 1;
        foreach ($animationTiles as $animationTile) {
            $x = $animationTile['x'] + 1;
            $y = $animationTile['y'] + 1;
            if ($y > $maxY) {
                $maxY = $y;
            }

            if ($x > $maxX) {
                $maxX = $x;
            }

            if (count($animationTile['tiles']) > $tilesInRow) {
                $tilesInRow = count($animationTile['tiles']);
            }
        }

        return new ImageSize(
            $maxX * $options->tileWidth * $tilesInRow,
            $maxY * $options->tileHeight,
        );
    }

    public function determineStaticTileset(array $tiles, ImageSizeOptions $options): ImageSize
    {
        $maxY = 1;
        $maxX = 1;
        foreach ($tiles as $tile) {
            $x = $tile['x'] + 1;
            $y = $tile['y'] + 1;
            if ($y > $maxY) {
                $maxY = $y;
            }

            if ($x > $maxX) {
                $maxX = $x;
            }
        }

        return new ImageSize(
            $maxX * $options->tileWidth,
            $maxY * $options->tileHeight,
        );
    }
}
