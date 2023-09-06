<?php

declare(strict_types=1);

namespace App\Cut\Application;

use App\Cut\Command\Domain\CutIntoPartsContext;
use Imagick;

final class ImagickTileCutter implements TileCutter
{
    public function cut(string $inputPath, string $outputDirectory, CutIntoPartsContext $context): void
    {
        $imagick = new Imagick($inputPath);
        $details = $imagick->identifyImage();

        $this->validate($details, $context);

        $framesX = $details['geometry']['width'] / $context->tileWidth;
        $framesY = $details['geometry']['height'] / $context->tileHeight;

        for ($y = 0; $y < $framesY; $y++) {
            for ($x = 0; $x < $framesX; $x++) {
                $imagick = new Imagick($inputPath);

                $imagick->cropImage(
                    $context->tileWidth,
                    $context->tileHeight,
                    $x * $context->tileWidth,
                    $y * $context->tileHeight
                );

                $imagick->writeImage($outputDirectory . DIRECTORY_SEPARATOR . $x . '_' . $y . '.png');
            }
        }
    }

    private function validate(array $details, CutIntoPartsContext $context)
    {
        if (
            $details['geometry']['width'] % $context->tileWidth !== 0
            || $details['geometry']['height'] % $context->tileHeight !== 0
        ) {
            throw new \Exception('Width or height is not divisible by tile width');
        }
    }
}
