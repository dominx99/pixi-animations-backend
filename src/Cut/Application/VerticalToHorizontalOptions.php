<?php

declare(strict_types=1);

namespace App\Cut\Application;

final readonly class VerticalToHorizontalOptions
{
    public function __construct(
        public readonly int $framesX,
        public readonly int $framesY,
        // public readonly int $tileWidth,
        // public readonly int $tileHeight,
    ) {
    }

    public static function fromArray(array $options): self
    {
        return new self(
            framesX: $options['framesX'],
            framesY: $options['framesY'],
            // tileWidth: $options['tileWidth'],
            // tileHeight: $options['tileHeight'],
        );
    }
}
