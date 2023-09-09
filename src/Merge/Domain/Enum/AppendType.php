<?php

declare(strict_types=1);

namespace App\Merge\Domain\Enum;

enum AppendType: string
{
    case HORIZONTAL = 'horizontal';
    case VERTICAL = 'vertical';
}
