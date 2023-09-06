<?php

declare(strict_types=1);

namespace App\Cut\Application;

use App\Cut\Command\Domain\CutIntoPartsContext;
use App\Cut\Domain\ValueObject\Tile;
use App\Cut\Domain\ValueObject\TilesetMetadata;
use Imagick;

final class ImagickTileCutter implements TileCutter
{
    public function cut(string $inputPath, string $outputDirectory, CutIntoPartsContext $context): TilesetMetadata
    {
        $imagick = new Imagick($inputPath);
        $details = $imagick->identifyImage();

        $this->validate($details, $context);

        $framesX = $details['geometry']['width'] / $context->tileWidth;
        $framesY = $details['geometry']['height'] / $context->tileHeight;

        $metadata = new TilesetMetadata($framesX, $framesY, $context->tileWidth, $context->tileHeight);

        for ($y = 0; $y < $framesY; $y++) {
            for ($x = 0; $x < $framesX; $x++) {
                $imagick = new Imagick($inputPath);

                $imagick->cropImage(
                    $context->tileWidth,
                    $context->tileHeight,
                    $x * $context->tileWidth,
                    $y * $context->tileHeight
                );

                $filename = $x . '_' . $y . '.png';
                $imagick->writeImage($outputDirectory . DIRECTORY_SEPARATOR . $filename);

                $metadata->addTile(new Tile($x, $y, $filename));
            }
        }

        return $metadata;
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
