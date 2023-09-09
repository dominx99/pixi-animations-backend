<?php

declare(strict_types=1);

namespace App\Merge\Application;

use App\Merge\Domain\Enum\AppendType;

final readonly class MergeTilesetOptions
{
    public function __construct(
        public AppendType $appendType,
    ) {
    }
}
