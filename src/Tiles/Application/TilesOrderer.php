<?php

declare(strict_types=1);

namespace App\Tiles\Application;

interface TilesOrderer
{
    public function order(array $tiles, OrderConfig $config): array;
}
