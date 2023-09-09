<?php

declare(strict_types=1);

namespace App\Merge\Application;

use Imagick;

final class ImagickTilesetMerger implements TilesetMerger
{
    /** @param Imagick[] $tilesets */
    public function merge(array $tilesets, MergeTilesetOptions $options, string $outputPath): Imagick
    {
        $imagick = new Imagick();

        foreach ($tilesets as $tileset) {
            $imagick->addImage($tileset);
            $imagick->setImageFormat('png');
            $imagick->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
            $imagick->setImageBackgroundColor('transparent');
        }

        $imagick->resetIterator();
        $imagick = $imagick->appendImages(true);

        $imagick->writeImage($outputPath);

        return $imagick;
    }
}
