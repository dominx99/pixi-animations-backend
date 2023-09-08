<?php

declare(strict_types=1);

namespace App\Merge\Application;

final readonly class ImageSizeOptions
{
    public function __construct(public int $tileWidth, public int $tileHeight)
    {
    }
}
