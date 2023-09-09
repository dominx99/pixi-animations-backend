<?php

declare(strict_types=1);

namespace App\Merge\Application;

use Imagick;
use ImagickPixel;

final class ImagickTileMerger implements TileMerger
{
    public function __construct(private string $resourcesPath)
    {
    }

    /**
        * @param array $tiles
        [
            {
                x: 0,
                y: 0,
                tiles: [
                    {
                        x: 0,
                        y: 0,
                        path: '0_0.png'
                    },
                    {
                        x: 0,
                        y: 1,
                        path: '0_1.png'
                    }
                ]
            }
        ]
     */
    public function merge(array $animationTiles, MergeOptions $options): Imagick
    {
        $imagick = new Imagick();
        $imagick->newImage($options->imageWidth, $options->imageHeight, new ImagickPixel('transparent'));
        $imagick->setImageFormat('png');
        $imagick->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
        $iteration = 1;

        foreach ($animationTiles as $animationTile) {
            $iteration = 1;
            foreach ($animationTile['tiles'] as $tile) {
                $source = $this->resourcesPath . DIRECTORY_SEPARATOR . $tile['path'];
                $tileImagick = new Imagick($source);

                $moveX = $animationTile['x'] > 0 ? $animationTile['x'] : 0; // 0, 1, 2, 3
                $moveX2 = ($options->tileWidth * $options->framesX * ($iteration - 1));
                $x = ($moveX * $options->tileWidth) + ($moveX2 > 0 ? $moveX2 : 0);
                $y = $animationTile['y'] * $options->tileHeight;

                $imagick->compositeImage(
                    $tileImagick,
                    Imagick::COMPOSITE_DEFAULT,
                    $x,
                    $y,
                );
                ++$iteration;
            }
        }

        return $imagick;
    }
}
