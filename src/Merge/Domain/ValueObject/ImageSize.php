<?php

declare(strict_types=1);

namespace App\Merge\Domain\ValueObject;

final readonly class ImageSize
{
    public function __construct(public readonly int $width, public readonly int $height)
    {
    }
}
