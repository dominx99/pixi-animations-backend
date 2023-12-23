<?php

declare(strict_types=1);

namespace App\Tiles\Application;

use App\Merge\Domain\ValueObject\ImageSize;
use Imagick;

final class ImagickImageSizeDeterminant
{
    public function determine(string $path): ImageSize
    {
        $imagick = new Imagick($path);

        return new ImageSize(
            $imagick->getImageWidth(),
            $imagick->getImageHeight()
        );
    }
}
