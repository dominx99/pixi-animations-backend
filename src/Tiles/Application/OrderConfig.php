<?php

declare(strict_types=1);

namespace App\Tiles\Application;

final readonly class OrderConfig
{
    public function __construct(public int $framesX, public int $framesY)
    {
    }
}
