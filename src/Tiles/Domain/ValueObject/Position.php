<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

final readonly class Position
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}
