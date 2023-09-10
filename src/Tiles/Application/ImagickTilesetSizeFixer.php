<?php

declare(strict_types=1);

namespace App\Tiles\Application;

use Imagick;
use ZipArchive;

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge(
            [],
            ...[$files, rglob($dir . "/" . basename($pattern), $flags)]
        );
    }
    return $files;
}

final class ImagickTilesetSizeFixer implements TilesetSizeFixer
{
    /** @param callable(string): void $logger */
    public function fix(string $inputPath, int $tileWidth, int $tileHeight, callable $logger): string
    {
        // $directoryToFix = sprintf('%s/%s', sys_get_temp_dir(), uniqid());
        $directoryToFix = $inputPath;

        $i = 0;

        foreach (rglob(sprintf('%s/*.png', $directoryToFix)) as $file) {
            $i++;
            $imagick = new Imagick($file);
            $details = $imagick->identifyImage();

            if ($this->isSizeCorrect($details, $tileWidth, $tileHeight)) {
                continue;
            }

            $logger(sprintf('Fixing %s, from %sx%s', $file, $details['geometry']['width'], $details['geometry']['height']));

            $this->fixSizeUsingImagick($imagick, $tileWidth, $tileHeight);
        }

        return $directoryToFix;
    }

    private function isSizeCorrect(array $details, int $tileWidth, int $tileHeight): bool
    {
        if (
            ($details['geometry']['width'] % $tileWidth) !== 0
            || ($details['geometry']['height'] % $tileHeight) !== 0
        ) {
            return false;
        }

        return true;
    }

    private function fixSizeUsingImagick(Imagick $imagick, int $tileWidth, int $tileHeight): void
    {
        $imagick->setImageBackgroundColor('transparent');
        $imagick->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);

        $imagick->extentImage(
            $imagick->getImageWidth() + ($tileWidth - $imagick->getImageWidth() % $tileWidth),
            $imagick->getImageHeight() + ($tileHeight - $imagick->getImageHeight() % $tileHeight),
            0,
            0
        );

        $imagick->writeImage();
    }
}
