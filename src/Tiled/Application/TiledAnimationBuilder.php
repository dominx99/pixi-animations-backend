<?php

declare(strict_types=1);

namespace App\Tiled\Application;

use App\Tiled\Domain\ValueObject\Tileset;
use Doctrine\Common\Collections\Collection;

interface TiledAnimationBuilder
{
    public function build(Tileset $tileset, Collection $animatedTiles): Tileset;
}
